<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Classes\Helper;
use Entrust;
use Form;

trait BasicController {

    public function logActivity($data) {
    	$data['user_id'] = isset($data['user_id']) ? $data['user_id'] : ((\Auth::check()) ? \Auth::user()->id : null);
    	$data['ip'] = \Request::getClientIp();
        $data['secondary_id'] = isset($data['secondary_id']) ? $data['secondary_id'] : null;
    	$activity = \App\Activity::create($data);
    }

    public function logEmail($data){
        $data['to_address'] = $data['to'];
        unset($data['to']);
        $data['from_address'] = config('mail.from.address');
        \App\Email::create($data);
    }

    public function designationAccessible($designation){
        if(Entrust::can('manage_all_designation') || (Entrust::can('manage_subordinate_designation') && Helper::isChild($designation->id)))
            return 1;
        else
            return 0;
    }

    public function employeeAccessible($employee){
    	if(Entrust::can('manage_all_employee') || (Entrust::can('manage_subordinate_employee') && Helper::isChild($employee->designation_id)))
    		return 1;
    	else
    		return 0;
    }

    public function expenseAccessible($expense){
        if(Entrust::can('manage_all_expense') || (Entrust::can('manage_subordinate_expense') && Helper::isChild($expense->User->designation_id)) || $expense->user_id = \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function AnnouncementAccessible($announcement){
        if(Entrust::can('manage_all_announcement') || (Entrust::can('manage_subordinate_announcement') && (Helper::isChild($announcement->User->designation_id) || $announcement->user_id == \Auth::user()->id)))
            return 1;
        else
            return 0;
    }

    public function awardAccessible($award){
        if(Entrust::can('manage_all_award') || (Entrust::can('manage_subordinate_award') && (Helper::isChild($award->ByUser->designation_id) || $award->user_id == \Auth::user()->id)) || $award->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function dailyReportAccessible($daily_report){
        if(Entrust::can('manage_all_daily_report') || (Entrust::can('manage_subordinate_daily_report') && (Helper::isChild($daily_report->User->designation_id) || $daily_report->user_id == \Auth::user()->id)) || $daily_report->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function taskAccessible($task){
        if(Entrust::can('manage_all_task') || (Entrust::can('manage_subordinate_task') && (Helper::isChild($task->UserAdded->designation_id) || $task->user_id == \Auth::user()->id)) || $task->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function ticketAccessible($ticket){
        if(Entrust::can('manage_all_ticket') || (Entrust::can('manage_subordinate_ticket') && (Helper::isChild($ticket->UserAdded->designation_id) || $ticket->user_id == \Auth::user()->id)) || $ticket->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function leaveAccessible($leave){
        if(Entrust::can('manage_all_leave') || (Entrust::can('manage_subordinate_leave') && (Helper::isChild($leave->User->designation_id) || $leave->user_id == \Auth::user()->id)) || $leave->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function jobAccessible($job){
        if(Entrust::can('manage_all_job') || (Entrust::can('manage_subordinate_job') && (Helper::isChild($job->User->designation_id) || $job->user_id == \Auth::user()->id)) || $job->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function jobApplicationAccessible($job_application){
        if(Entrust::can('manage_job_application') && (Entrust::can('manage_all_job') || (Entrust::can('manage_subordinate_job') && (Helper::isChild($job_application->Job->designation_id) || $job_application->Job->user_id == \Auth::user()->id)) || $job_application->Job->user_id == \Auth::user()->id))
            return 1;
        else
            return 0;
    }

    public function getAttendanceSummary($user,$from_date,$to_date){

        $clocks = \App\Clock::where('date','>=',$from_date)->where('date','<=',$to_date)->get();
        $holidays = \App\Holiday::where('date','>=',$from_date)->where('date','<=',$to_date)->get();

        $leave_approved = array();

        $leaves = \App\Leave::whereUserId($user->id)->whereStatus('approved')->where(function($query) use($from_date,$to_date) {
            $query->whereBetween('from_date',array($from_date,$to_date))
            ->orWhereBetween('to_date',array($from_date,$to_date))
            ->orWhere(function($query1) use($from_date,$to_date) {
                $query1->where('from_date','<',$from_date)
                ->where('to_date','>',$to_date);
            });
        })->get();
        foreach($leaves as $leave){
            $leave_approved_dates = ($leave->approved_date) ? explode(',',$leave->approved_date) : [];
            foreach($leave_approved_dates as $leave_approved_date)
                $leave_approved[] = $leave_approved_date;
        }

        $total_late = 0;
        $total_early = 0;
        $total_overtime = 0;
        $total_working = 0;
        $total_rest = 0;

        $date = $from_date;
        $tag_count = array();
        while($date <= $to_date){
            $tag = '';
            $late = 0;
            $early = 0;
            $working = 0;
            $overtime = 0;
            $rest = 0;
            
            $my_shift = Helper::getShift($date,$user->id);
            $my_shift->in_time = $date.' '.$my_shift->in_time;
            
            if($my_shift->overnight)
                $my_shift->out_time = date('Y-m-d',strtotime($date . ' +1 days')).' '.$my_shift->out_time;
            else
                $my_shift->out_time = $date.' '.$my_shift->out_time;

            $out = $clocks->whereLoose('date',$date)->whereLoose('user_id',$user->id)->sortBy('clock_in')->last();
            $in = $clocks->whereLoose('date',$date)->whereLoose('user_id',$user->id)->sortBy('clock_in')->first();
            $records = $clocks->whereLoose('date',$date)->whereLoose('user_id',$user->id)->all();

            $late = (isset($in) && (strtotime($in->clock_in) > strtotime($my_shift->in_time)) && $my_shift->in_time != $my_shift->out_time) ? abs(strtotime($my_shift->in_time) - strtotime($in->clock_in)) : 0;

            if($late){
                $tag_count[] = 'L';
                $tag .= Helper::getAttendanceTag('late');
            }

            $total_late += $late;
            $early = (isset($out) && $out->clock_out != null && (strtotime($out->clock_out) < strtotime($my_shift->out_time)) && $my_shift->in_time != $my_shift->out_time) ? abs(strtotime($my_shift->out_time) - strtotime($out->clock_out)) : 0;

            if($early){
                $tag_count[] = 'E';
                $tag .= Helper::getAttendanceTag('early');
            }

            $total_early += $early;
            
            foreach($records as $record){
                if($record->clock_in >= $my_shift->out_time && $record->clock_out != null)
                    $overtime += strtotime($record->clock_out) - strtotime($record->clock_in);
                elseif($record->clock_in < $my_shift->out_time && $record->clock_out > $my_shift->out_time)
                    $overtime += strtotime($record->clock_out) - strtotime($my_shift->out_time);
            }

            if($overtime){
                $tag_count[] = 'O';
                $tag .= Helper::getAttendanceTag('overtime');
            }

            $total_overtime += $overtime;

            foreach($records as $record)
                $working += ($record->clock_out != null) ? abs(strtotime($record->clock_out) - strtotime($record->clock_in)) : 0;
            $total_working += $working;

            $rest = (isset($in) && $out->clock_out != null) ? (abs(strtotime($out->clock_out) - strtotime($in->clock_in)) - $working) : 0;
            $total_rest += $rest;

            $holiday = $holidays->whereLoose('date',$date)->first();

            if(isset($in)){
                $attendance = 'P';
                $attendance_label = '<span class="badge badge-success">'.trans('messages.present').'</span>';
            } elseif(count($leave_approved) && in_array($date,$leave_approved)){
                $attendance = 'L';
                $attendance_label = '<span class="badge badge-warning">'.trans('messages.leave').'</span>';
            } elseif($holiday){
                $attendance = 'H';
                $attendance_label = '<span class="badge badge-info">'.trans('messages.holiday').'</span>';
            } elseif(!$holiday && $date < date('Y-m-d')){
                $attendance = 'A';
                $attendance_label = '<span class="badge badge-danger">'.trans('messages.absent').'</span>';
            } else {
                $attendance = '';
                $attendance_label = '';
            }

            $cols_summary[$date] = $attendance;
            $date = date('Y-m-d',strtotime($date . ' +1 days'));
        }

        $total['total_late'] = $total_late;
        $total['total_early'] = $total_early;
        $total['total_working'] = $total_working;
        $total['total_rest'] = $total_rest;
        $total['total_overtime'] = $total_overtime;

        $summary['total_late'] = Helper::showDuration($total_late);
        $summary['total_early'] = Helper::showDuration($total_early);
        $summary['total_working'] = Helper::showDuration($total_working);
        $summary['total_rest'] = Helper::showDuration($total_rest);
        $summary['total_overtime'] = Helper::showDuration($total_overtime);

        $cols_summary = array_count_values($cols_summary);
        $tag_summary = array_count_values($tag_count);
        
        $att_summary['A'] = array_key_exists('A', $cols_summary) ? $cols_summary['A'] : 0;
        $att_summary['H'] = array_key_exists('H', $cols_summary) ? $cols_summary['H'] : 0;
        $att_summary['P'] = array_key_exists('P', $cols_summary) ? $cols_summary['P'] : 0;
        $att_summary['L'] = array_key_exists('L', $cols_summary) ? $cols_summary['L'] : 0;
        $att_summary['Late'] = array_key_exists('L', $tag_summary) ? $tag_summary['L'] : 0;
        $att_summary['Early'] = array_key_exists('E', $tag_summary) ? $tag_summary['E'] : 0;
        $att_summary['Overtime'] = array_key_exists('O', $tag_summary) ? $tag_summary['O'] : 0;
        $att_summary['W'] = $att_summary['H'] + $att_summary['P'];

        return ['summary' => $summary,'att_summary' => $att_summary,'total' => $total];
    }

    public function getLeaveBalance($user,$date){
        $leave_types = \App\LeaveType::all();
        $contract = Helper::getContract($user->id,$date);
        $user_leaves = \App\Leave::whereUserId($user->id)->whereStatus('approved')->get();

        $used = array();
        $allotted = array();

        foreach($leave_types as $leave_type){
            $used[$leave_type->id] = 0;

            $allotted[$leave_type->id] = ($contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->count()) ? $contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->first()->leave_count : 0;
        }

        $approved_date = array();
        foreach($user_leaves as $user_leave){
            
            if($user_leave->approved_date)
                $approved_date = explode(',',$user_leave->approved_date);
            else {
                $leave_from_date = $user_leave->from_date;
                $leave_to_date = $user_leave->to_date;
                while($leave_from_date <= $leave_to_date){
                    $approved_date[] = $leave_from_date;
                    $leave_from_date = date('Y-m-d',strtotime($leave_from_date . ' +1 day'));
                }
            }

            foreach($approved_date as $approved){
                if($approved >= $contract->from_date && $approved <= $contract->to_date)
                    $used[$user_leave->leave_type_id]++;
            }
            unset($approved_date);
        }

        $data['used'] = $used;
        $data['allotted'] = $allotted;

        return $data;
    }
}