<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Classes\Helper;
use App\Http\Controllers\BasicController;
use PDF;

class GeneratePayroll extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, BasicController;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $from_date;
    protected $to_date;
    protected $send_mail;

    public function __construct($from_date,$to_date,$send_mail)
    {
         $this->from_date = $from_date;
         $this->to_date = $to_date;
         $this->send_mail = $send_mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $salary_types = \App\SalaryType::all();
        $leave_types = \App\LeaveType::all();
        $earning_salary_types = \App\SalaryType::where('salary_type','=','earning')->get();
        $deduction_salary_types = \App\SalaryType::where('salary_type','=','deduction')->get();
        $template = \App\Template::whereCategory('payslip_email')->first();
        
        $from_date_month = date('m',strtotime($this->from_date));
        $to_date_month = date('m',strtotime($this->to_date));
        $from_date_year = date('Y',strtotime($this->from_date));
        $to_date_year = date('Y',strtotime($this->to_date));
        
        if($from_date_month != $to_date_month){
          $payroll_days = (config('config.payroll_days') == 'from_date') ? cal_days_in_month(CAL_GREGORIAN, $from_date_month, $from_date_year) : cal_days_in_month(CAL_GREGORIAN, $to_date_month, $to_date_year);
        } else
          $payroll_days = cal_days_in_month(CAL_GREGORIAN, $from_date_month, $from_date_year);

        foreach(\App\User::all() as $user){
            $count = \App\PayrollSlip::whereUserId($user->id)->
            where(function ($query) { $query->where(function ($query) {
              $query->where('from_date','>=',$this->from_date)
              ->where('from_date','<=',$this->to_date);
            })->orWhere(function ($query) {
              $query->where('to_date','>=',$this->from_date)
                ->where('to_date','<=',$this->to_date);
            });})->count();

            if(!$count){
                $contract = Helper::getContract($user->id,$this->from_date);
                if(isset($contract) && $contract->to_date >= $this->to_date){
                    $data = $this->getAttendanceSummary($user,$this->from_date,$this->to_date);
                    $att_summary = $data['att_summary'];
                    $summary = $data['summary'];
                    $total = $data['total'];
                    $working_day = $att_summary['P'] + $att_summary['L'] + $att_summary['H'];

                    $payroll_slip = new \App\PayrollSlip;
                    $payroll_slip->user_id = $user->id;
                    $payroll_slip->from_date = $this->from_date;
                    $payroll_slip->to_date = $this->to_date;

                    $payroll_slip->hourly = ($contract->hourly_payroll) ? (floor($total['total_working'] / 3600) * $contract->hourly_rate) : 0;
                    $payroll_slip->late = (!$contract->hourly_payroll) ? (floor($total['total_late'] / 3600) * $contract->late_hourly_rate) : 0;
                    $payroll_slip->overtime = (!$contract->hourly_payroll) ? (floor($total['total_overtime'] / 3600) * $contract->overtime_hourly_rate) : 0;
                    $payroll_slip->early_leaving = (!$contract->hourly_payroll) ? (floor($total['total_early'] / 3600) * $contract->early_leaving_hourly_rate) : 0;
                    $payroll_slip->hourly_payroll = ($contract->hourly_payroll) ? 1 : 0;
                    $payroll_slip->save();

                    foreach($salary_types as $salary_type){
                        $salary = ($contract->Salary->whereLoose('salary_type_id',$salary_type->id)->count()) ? ($contract->Salary->whereLoose('salary_type_id',$salary_type->id)->first()->amount) : 0;
                        if(!$salary_type->is_fixed)
                            $salary = ($salary/$payroll_days)*$working_day;
                        $payroll_salary = new \App\Payroll;
                        $payroll_salary->payroll_slip_id = $payroll_slip->id;
                        $payroll_salary->salary_type_id = $salary_type->id;
                        $payroll_salary->amount = (!$contract->hourly_payroll) ? $salary : 0;
                        $payroll_salary->save();
                    }

                    if($this->send_mail) {
                        
                        $payroll = $payroll_slip->Payroll->pluck('amount','salary_type_id')->all();

                        $leave_data = $this->getLeaveBalance($user,$payroll_slip->from_date);
                        $used = $leave_data['used'];
                        $allotted = $leave_data['allotted'];

                        $data = [
                            'user' => $user,
                            'payroll' => $payroll,
                            'earning_salary_types' => $earning_salary_types,
                            'deduction_salary_types' => $deduction_salary_types,
                            'payroll_slip' => $payroll_slip,
                            'total_earning' => 0,
                            'total_deduction' => 0,
                            'summary' => $summary,
                            'att_summary' => $att_summary,
                            'used' => $used,
                            'leave_types' => $leave_types,
                            'allotted' => $allotted,
                            'contract' => $contract
                        ];

                        $pdf = PDF::loadView('payroll.pdf', $data);

                        $mail = array();
                        if(!$template){
                          $mail['subject'] = config('template.payslip.default_subject');
                          $body = 'Please find payslip in the attachment for duration '.showDate($payroll_slip->from_date).' to '.showDate($payroll_slip->to_date);
                        } else {
                          $mail['subject'] = $template->subject.' duration '.showDate($payroll_slip->from_date).' to '.showDate($payroll_slip->to_date);
                          $body = $template->body;
                              $body = str_replace('[NAME]',$user->full_name,$body);
                              $body = str_replace('[USERNAME]',$user->username,$body);
                              $body = str_replace('[EMAIL]',$user->email,$body);
                              $body = str_replace('[DESIGNATION]',$user->Designation->name,$body);
                              $body = str_replace('[DEPARTMENT]',$user->Designation->Department->name,$body);
                              $body = str_replace('[FROM_DATE]',showDate($payroll_slip->from_date),$body);
                              $body = str_replace('[TO_DATE]',showDate($payroll_slip->to_date),$body);
                              $body = str_replace('[DATE_GENERATED]',showDateTime($payroll_slip->created_at),$body);
                        }
                        $mail['email'] = $user->email;
                        $mail['filename'] = 'Payslip_'.$payroll_slip->id.'.pdf';
                        \Mail::send('emails.email', compact('body'), function ($message) use($pdf,$mail) {
                          $message->attachData($pdf->output(), $mail['filename']);
                          $message->to($mail['email'])->subject($mail['subject']);
                        });
                    }
                }
            }
        }
    }
}
