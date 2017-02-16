<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\PayrollRequest;
use DB;
use Entrust;
use Auth;
use PDF;
use App\User;
use App\PayrollSlip;
use App\SalaryType;
use App\Salary;
use App\Clock;
use App\Holiday;
use App\Payroll;
use App\Classes\Helper;
use Validator;
use App\Jobs\GeneratePayroll;

Class PayrollController extends Controller{
    use BasicController;
	protected $form = 'payroll-form';

	public function __construct()
	{
		$this->middleware('officeshift');
	}

	public function test(){
	}

	public function index(){

	$col_heads = array(
			trans('messages.option'),
	        trans('messages.slip'),
	        trans('messages.name'),
	        trans('messages.date'),
			trans('messages.duration'),
			);

	array_push($col_heads,trans('messages.hourly').' '.trans('messages.salary'));
	array_push($col_heads,trans('messages.overtime').' '.trans('messages.salary'));
	array_push($col_heads,trans('messages.late').' '.trans('messages.salary').' '.trans('messages.deduction'));
	array_push($col_heads,trans('messages.early_leaving').' '.trans('messages.salary').' '.trans('messages.deduction'));

	$salary_types = SalaryType::all();
	foreach($salary_types as $salary_type)
	  array_push($col_heads,$salary_type->head);

	if(config('config.payroll_contribution_field')){
	    array_push($col_heads,trans('messages.date_of_contribution'));
	    array_push($col_heads,trans('messages.employer_contribution'));
	    array_push($col_heads,trans('messages.employee_contribution'));
	}
	array_push($col_heads,trans('messages.total'));
	$col_heads = Helper::putCustomHeads($this->form, $col_heads);

	$table_info = array(
	  'source' => 'payroll',
	  'title' => 'Payroll List',
	  'id' => 'payroll_table'
	);
	$menu = ['payroll'];

		return view('payroll.index',compact('col_heads','menu','table_info'));
	}

	public function lists(Request $request){

	  if(Entrust::can('manage_all_employee'))
	    $payroll_slips = PayrollSlip::all();
	  elseif(Entrust::can('manage_subordinate_employee')){
	    $child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
	    $child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
	    array_push($child_users, Auth::user()->id);
	    $payroll_slips = PayrollSlip::whereIn('user_id',$child_users)->get();
	  } else {
	    $payroll_slips = PayrollSlip::where('user_id','=',Auth::user()->id)->get();
	  }

	  $rows = array();
	  $col_ids = Helper::getCustomColId($this->form);
	  $values = Helper::fetchCustomValues($this->form);
	  $salary_types = SalaryType::all();
	  $sum_total = 0;
	  foreach($payroll_slips as $payroll_slip){

	    $amount = array();
	    $sum_amount = array();
	    $total = 0;

	    foreach($salary_types as $salary_type){
	      $amount[$salary_type->id] = 0;
	      $sum_amount[$salary_type->id] = 0;
	    }

	    foreach($payroll_slip->Payroll as $payroll){
	      $amount[$payroll->salary_type_id] = round($payroll->amount,2);
	      $sum_amount[$payroll->salary_type_id] += round($payroll->amount,2);
	    }

	    foreach($salary_types as $salary_type){
	      if($salary_type->salary_type == "earning")
	        $total += $amount[$salary_type->id];
	      else
	        $total -= $amount[$salary_type->id];
	    }

	    $total += $payroll_slip->hourly;
	    $total += $payroll_slip->overtime;
	    $total -= $payroll_slip->late;
	    $total -= $payroll_slip->early_leaving;

	    $row = array(
	        '<div class="btn-group btn-group-xs">'.
	          '<a href="/payroll/'.$payroll_slip->id.'" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-o-right"></i></a>'.
	          (Entrust::can('generate_payroll') ? delete_form(['payroll.destroy',$payroll_slip->id]) : '').'</div>',
	        $payroll_slip->id,
	        $payroll_slip->User->full_name_with_designation,
	        date('d M Y',strtotime($payroll_slip->created_at)),
	        showDate($payroll_slip->from_date).' '.trans('messages.to').' '.showDate($payroll_slip->to_date),
	        );  

	    array_push($row,currency($payroll_slip->hourly));
	    array_push($row,currency($payroll_slip->overtime));
	    array_push($row,currency($payroll_slip->late));
	    array_push($row,currency($payroll_slip->early_leaving));

	    foreach($amount as $value)
	      array_push($row,currency($value));
	    
		if(config('config.payroll_contribution_field')){
	        array_push($row,showDate($payroll_slip->date_of_contribution));
	        array_push($row,currency($payroll_slip->employer_contribution));
	        array_push($row,currency($payroll_slip->employee_contribution));
	    }
	    array_push($row,currency($total));

	    $id = $payroll_slip->id;
	    
	    $sum_total += $total;
	    unset($amount);

		foreach($col_ids as $col_id)
			array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');

	    $rows[] = $row;
	  }

	  $list['aaData'] = $rows;
	  return json_encode($list);
	}

	public function customReport(){

	$col_heads = array(
	        trans('messages.employee_code'),
	        trans('messages.duration'),
	        trans('messages.account_number'),
	        trans('messages.bank_name'),
	        trans('messages.bank_code'),
	        trans('messages.employee'),
	        trans('messages.date_of_joining'),
	        trans('messages.designation'),
	        trans('messages.department'),
			);

	$col_heads = Helper::putCustomHeads($this->form, $col_heads);
	
	array_push($col_heads,trans('messages.day'));
	array_push($col_heads,trans('messages.salary'));

	$leave_types = \App\LeaveType::all();
	foreach($leave_types as $leave_type)
	  array_push($col_heads,$leave_type->name);

	$table_info = array(
	  'source' => 'payroll-custom-report',
	  'title' => 'Payroll List',
	  'id' => 'payroll_table'
	);
	$menu = ['payroll'];

		return view('payroll.index',compact('col_heads','menu','table_info'));
	}

	public function customReportLists(Request $request){

	  if(Entrust::can('manage_all_employee'))
	    $payroll_slips = PayrollSlip::all();
	  elseif(Entrust::can('manage_subordinate_employee')){
	    $child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
	    $child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
	    array_push($child_users, Auth::user()->id);
	    $payroll_slips = PayrollSlip::whereIn('user_id',$child_users)->get();
	  } else {
	    $payroll_slips = PayrollSlip::where('user_id','=',Auth::user()->id)->get();
	  }

	  $leave_types = \App\LeaveType::all();

	  $rows = array();
	  $col_ids = Helper::getCustomColId($this->form);
	  $values = Helper::fetchCustomValues($this->form);
	  $salary_types = SalaryType::all();
	  $sum_total = 0;

	  foreach($payroll_slips as $payroll_slip){
	  	$holidays = \App\Holiday::where('date','>=',$payroll_slip->from_date)->where('date','<=',$payroll_slip->to_date)->get()->pluck('date')->all();

	    $amount = array();
	    $sum_amount = array();
	    $total = 0;

	    foreach($salary_types as $salary_type){
	      $amount[$salary_type->id] = 0;
	      $sum_amount[$salary_type->id] = 0;
	    }

	    foreach($payroll_slip->Payroll as $payroll){
	      $amount[$payroll->salary_type_id] = round($payroll->amount,2);
	      $sum_amount[$payroll->salary_type_id] += round($payroll->amount,2);
	    }

	    foreach($salary_types as $salary_type){
	      if($salary_type->salary_type == "earning")
	        $total += $amount[$salary_type->id];
	      else
	        $total -= $amount[$salary_type->id];
	    }

	    $clocks = \App\Clock::whereUserId($payroll_slip->User->id)->where('date','>=',$payroll_slip->from_date)->where('date','<=',$payroll_slip->to_date)->get()->pluck('date')->all();

	    $working = array_merge($holidays,$clocks);
	    $days = count(array_unique($working));

	    $leave_data = $this->getLeaveBalance($payroll_slip->User,$payroll_slip->from_date);
	    $used = $leave_data['used'];
	    $allotted = $leave_data['allotted'];

	    $bank_account = $payroll_slip->User->BankAccount->where('is_primary',1)->first();

	    $row = array(
	        $payroll_slip->User->Profile->employee_code,
	        showDate($payroll_slip->from_date).' '.trans('messages.to').' '.showDate($payroll_slip->to_date),
	        ($bank_account ? $bank_account->account_number : '-'),
	        ($bank_account ? $bank_account->bank_name : '-'),
	        ($bank_account ? $bank_account->bank_code : '-'),
	        $payroll_slip->User->full_name,
	        showDate($payroll_slip->User->Profile->date_of_joining),
	        $payroll_slip->User->Designation->name,
	        $payroll_slip->User->Designation->Department->name,
	        );  

	    $id = $payroll_slip->id;
		foreach($col_ids as $col_id)
			array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');

		array_push($row,$days);
	    array_push($row,currency($total));

	    foreach($leave_types as $leave_type)
			array_push($row,$used[$leave_type->id].'/'.$allotted[$leave_type->id]);

	    unset($amount);
	    $rows[] = $row;
	  }

	  $list['aaData'] = $rows;
	  return json_encode($list);
	}

	public function show($id){

		$payroll_slip = PayrollSlip::find($id);

		if(!$payroll_slip)
			return redirect('/payroll')->withErrors(trans('messages.invalid_link'));

		$user = User::find($payroll_slip->user_id);
		if(!$this->employeeAccessible($user) && $payroll_slip->user_id != Auth::user()->id)
			return redirect('/payroll')->withErrors(trans('messages.invalid_link'));

    	$payroll = $payroll_slip->Payroll->pluck('amount','salary_type_id')->all();

        $contract = Helper::getContract($user->id,$payroll_slip->from_date);

    	$earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
   	 	$deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();
	    $salaries = Helper::getContract($user->id,$payroll_slip->from_date)->Salary;

		$data = $this->getAttendanceSummary($user,$payroll_slip->from_date,$payroll_slip->to_date);
		$summary = $data['summary'];
		$att_summary = $data['att_summary'];
		$total_earning = 0;
		$total_deduction = 0;

		return view('payroll.show',compact('payroll_slip','payroll','user','earning_salary_types','deduction_salary_types','summary','att_summary','salaries','contract','total_earning','total_deduction'));

	}

	public function create(Request $request){

		if(!Entrust::can('generate_payroll'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$from_date = $request->input('from_date') ? : '';
		$to_date = $request->input('to_date') ? : '';
		$user_id = $request->input('user_id') ? : '';

		if(defaultRole())
	      $users = \App\User::all()->pluck('full_name_with_designation','id')->all();
	    elseif(Entrust::can('manage_all_employee'))
	      $users = \App\User::whereIsHidden(0)->get()->pluck('full_name_with_designation','id')->all();
	    elseif(Entrust::can('manage_subordinate_employee')){
	      $child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
	      $child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
	      $users = \App\User::whereIn($child_users,'id')->get()->pluck('full_name_with_designation','id')->all();
	    } else
	      $users = \App\User::whereId(Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();

	    $menu = ['payroll'];
	    if(!$request->input('submit'))
	    	return view('payroll.create',compact('from_date','to_date','user_id','users','menu'));

		$validation = Validator::make($request->all(),[
		'user_id' => 'required',
		'from_date' => 'required|date|before_equal:to_date',
		'to_date' => 'required|date',
		]);

		if($validation->fails())
		  return redirect()->back()->withInput()->withErrors($validation->messages());

		$count = PayrollSlip::whereUserId($user_id)->
		where(function ($query) use($from_date,$to_date) { $query->where(function ($query) use($from_date,$to_date){
		  $query->where('from_date','>=',$from_date)
		  ->where('from_date','<=',$to_date);
		})->orWhere(function ($query)  use($from_date,$to_date) {
		  $query->where('to_date','>=',$from_date)
		    ->where('to_date','<=',$to_date);
		});})->count();

		if($count)
			return redirect()->back()->withInput()->withErrors(trans('messages.payroll_already_generated'));

	    $user = User::find($user_id);

	    $contract = Helper::getContract($user->id,$from_date);
		if(!$contract)
			return redirect()->back()->withInput()->withErrors(trans('messages.contract_period_not_found'));

		if($contract && $contract->to_date < $to_date)
			return redirect()->back()->withInput()->withErrors(trans('messages.change_in_contract_period'));

		$data = $this->getAttendanceSummary($user,$from_date,$to_date);
		$total = $data['total'];
		$summary = $data['summary'];
		$att_summary = $data['att_summary'];
		$working_days = $att_summary['P'] + $att_summary['L'] + $att_summary['H'];

	  	$no_of_days = dateDiff($to_date,$from_date);
	    $salary_fraction = ($no_of_days) ? ($att_summary['W'] / $no_of_days) : 0;
		
	    $earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
	    $deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();
	    $salaries = Helper::getContract($user->id,$from_date)->Salary;

		$from_date_month = date('m',strtotime($from_date));
		$to_date_month = date('m',strtotime($to_date));
		$from_date_year = date('Y',strtotime($from_date));
		$to_date_year = date('Y',strtotime($to_date));
		
		if($from_date_month != $to_date_month){
			$payroll_days = (config('config.payroll_days') == 'from_date') ? cal_days_in_month(CAL_GREGORIAN, $from_date_month, $from_date_year) : cal_days_in_month(CAL_GREGORIAN, $to_date_month, $to_date_year);
		} else
			$payroll_days = cal_days_in_month(CAL_GREGORIAN, $from_date_month, $from_date_year);

		$salary_values = array();

		foreach($earning_salary_types as $earning_salary_type)
			$salary_values[$earning_salary_type->id] = 0;
		foreach($deduction_salary_types as $deduction_salary_type)
			$salary_values[$deduction_salary_type->id] = 0;

		foreach($salaries as $salary){
			$salary_values[$salary->salary_type_id] = ($contract->hourly_payroll) ? 0 : (($salary->SalaryType->is_fixed) ? round($salary->amount,config('config.currency_decimal')) : round((($salary->amount/$payroll_days)*$working_days),config('config.currency_decimal')) );
		}

		$hourly_payroll = $contract->hourly_payroll;
		$hourly = round((floor($total['total_working'] / 3600) * $contract->hourly_rate),config('config.currency_decimal'));
		$late = (!$contract->hourly_payroll) ? round((floor($total['total_late'] / 3600) * $contract->late_hourly_rate),config('config.currency_decimal')) : 0;
		$overtime = (!$contract->hourly_payroll) ? round((floor($total['total_overtime'] / 3600) * $contract->overtime_hourly_rate),config('config.currency_decimal')) : 0;
		$early_leaving = (!$contract->hourly_payroll) ? round((floor($total['total_early'] / 3600) * $contract->early_leaving_hourly_rate),config('config.currency_decimal')) : 0;

		return view('payroll.create',compact('users','user','user_id','earning_salary_types','deduction_salary_types','salaries','summary','att_summary','salary_fraction','menu','from_date','to_date','salary_values','hourly','late','overtime','early_leaving','hourly_payroll'));
	}

	public function createMultiple(){
		if(!Entrust::can('generate_multiple_payroll'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		return view('payroll.multiple');
	}

	public function postCreateMultiple(Request $request){
		if(!Entrust::can('generate_multiple_payroll'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$validation = Validator::make($request->all(),[
		'from_date' => 'required|date|before_equal:to_date',
		'to_date' => 'required|date'
		]);

		if($validation->fails()){
			if($request->has('ajax_submit')){
			    $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
			    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
			}
			return redirect('/dashboard')->withErrors($validation->messages()->first());
		}

		$from_date = $request->input('from_date');
		$to_date = $request->input('to_date');
		$send_mail = ($request->has('send_mail')) ? 1 : 0;

		$this->dispatch(new GeneratePayroll($from_date,$to_date,$send_mail));

		if($request->has('ajax_submit')){
		    $response = ['message' => trans('messages.request_submit'), 'status' => 'success']; 
		    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		}
		return redirect('/dashboard')->withSuccess(trans('messages.request_submit'));
	}

	public function generate($action = 'print' , $payroll_slip_id){

		$payroll_slip = PayrollSlip::find($payroll_slip_id);

		if(!$payroll_slip)
			return redirect('/payroll')->withErrors(trans('messages.invalid_link'));

		$user = User::find($payroll_slip->user_id);
		if(!$this->employeeAccessible($user) && $payroll_slip->user_id != Auth::user()->id)
			return redirect('/payroll')->withErrors(trans('messages.invalid_link'));

		$payroll = $payroll_slip->Payroll->pluck('amount','salary_type_id')->all();

        $contract = Helper::getContract($user->id,$payroll_slip->from_date);

        $leave_types = \App\LeaveType::all();
    	$earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
   	 	$deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();
		$summary_data = $this->getAttendanceSummary($user,$payroll_slip->from_date,$payroll_slip->to_date);
		$summary = $summary_data['summary'];
		$att_summary = $summary_data['att_summary'];

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

   	 	if($action == 'mail'){
	   	 	$pdf = PDF::loadView('payroll.pdf', $data);
	   	 	$template = \App\Template::whereCategory('payslip_email')->first();

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
	   	 	return redirect('/payroll')->withSuccess(trans('messages.mail').' '.trans('messages.sent'));
   	 	} elseif($action == 'pdf') {
	   	 	$pdf = PDF::loadView('payroll.pdf', $data);
			return $pdf->download('payslip.pdf');
   	 	} else
    	return view('payroll.pdf',$data);
	}

	public function edit($id){
		$payroll_slip = PayrollSlip::find($id);
		$user = $payroll_slip->User;

		if(!$payroll_slip || !$this->employeeAccessible($user) && $payroll_slip->user_id != Auth::user()->id)
            return view('common.error',['message' => trans('messages.permission_denied')]);

        $payrolls = $payroll_slip->Payroll->pluck('amount','salary_type_id')->all();

	    $earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
	    $deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();
		$custom_field_values = Helper::getCustomFieldValues($this->form,$payroll_slip->id);

		$salary_values = array();

		foreach($earning_salary_types as $earning_salary_type)
			$salary_values[$earning_salary_type->id] = 0;
		foreach($deduction_salary_types as $deduction_salary_type)
			$salary_values[$deduction_salary_type->id] = 0;

		foreach($payrolls as $key => $payroll){
			$salary_values[$key] = ($payroll_slip->hourly_payroll) ? 0 : (round($payroll,config('config.currency_decimal')));
		}

		$hourly_payroll = $payroll_slip->hourly_payroll;
		$hourly = round($payroll_slip->hourly,config('config.currency_decimal'));
		$late = (!$payroll_slip->hourly_payroll) ? round($payroll_slip->late,config('config.currency_decimal')) : 0;
		$overtime = (!$payroll_slip->hourly_payroll) ? round($payroll_slip->overtime,config('config.currency_decimal')) : 0;
		$early_leaving = (!$payroll_slip->hourly_payroll) ? round($payroll_slip->early_leaving,config('config.currency_decimal')) : 0;

        return view('payroll.edit',compact('payroll_slip','earning_salary_types','deduction_salary_types','payroll','custom_field_values','salary_values','hourly','late','overtime','hourly_payroll','early_leaving'));
	}

	public function update(Request $request, $id){
		$payroll_slip = PayrollSlip::find($id);
		$user = $payroll_slip->User;
		$from_date = $payroll_slip->from_date;
		$to_date = $payroll_slip->to_date;

		if(!Entrust::can('generate_payroll'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		if(!$payroll_slip || !$this->employeeAccessible($user) && $payroll_slip->user_id != Auth::user()->id){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors(trans('messages.permission_denied'));
		}

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		$salary_types = SalaryType::all();

		$payroll_slip = PayrollSlip::firstOrNew(['id' => $id]);
		$payroll_slip->hourly_payroll = ($request->has('hourly_payroll')) ? 1 : 0;
		$payroll_slip->hourly = ($request->has('hourly_payroll')) ? $request->input('hourly') : 0;
		$payroll_slip->late = (!$request->has('hourly_payroll')) ? $request->input('late') : 0;
		$payroll_slip->overtime = (!$request->has('hourly_payroll')) ? $request->input('overtime') : 0;
		$payroll_slip->early_leaving = (!$request->has('hourly_payroll')) ? $request->input('early_leaving') : 0;

		if($request->has('employee_contribution'))
	    $payroll_slip->employee_contribution = $request->input('employee_contribution');
		if($request->has('employer_contribution'))
	    $payroll_slip->employer_contribution = $request->input('employer_contribution');
		if($request->has('date_of_contribution'))
	    $payroll_slip->date_of_contribution = $request->input('date_of_contribution') ? : null;
		$payroll_slip->save();

		foreach($salary_types as $salary_type){
			$salary = Payroll::firstOrCreate(array(
				'payroll_slip_id' => $payroll_slip->id,
				'salary_type_id' => $salary_type->id
				));
			$salary->payroll_slip_id = $payroll_slip->id;
			$salary->salary_type_id = $salary_type->id;
			$salary->amount = (!$request->has('hourly_payroll')) ? $request->input($salary_type->id) : 0;
			$salary->save();
		}
		$data = $request->all();
		Helper::updateCustomField($this->form,$payroll_slip->id, $data);

	    $this->logActivity(['module' => 'payroll','unique_id' => $payroll_slip->id,'activity' => 'activity_updated']);
	    
		if($request->has('ajax_submit')){
		  	$response = ['message' => trans('messages.payroll').' '.trans('messages.saved'), 'status' => 'success']; 
		  	return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		}
		return redirect('/payroll')->withSuccess(trans('messages.payroll').' '.trans('messages.saved'));
	}

	public function store(PayrollRequest $request){

		if(!Entrust::can('generate_payroll'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		$count = PayrollSlip::whereUserId($request->input('user_id'))->
		where(function ($query) use($request) { $query->where(function ($query) use($request){
		  $query->where('from_date','>=',$request->input('from_date'))
		  ->where('from_date','<=',$request->input('to_date'));
		})->orWhere(function ($query)  use($request) {
		  $query->where('to_date','>=',$request->input('from_date'))
		    ->where('to_date','<=',$request->input('to_date'));
		});})->count();

		if($count){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.payroll_already_generated'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
			return redirect()->back()->withInput()->withErrors(trans('messages.payroll_already_generated'));
		}

	    $user = User::find($request->input('user_id'));

	    $contract = Helper::getContract($user->id,$request->input('from_date'));
		if(!$contract){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.contract_period_not_found'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
			return redirect()->back()->withInput()->withErrors(trans('messages.contract_period_not_found'));
		}

		if($contract && $contract->to_date < $request->input('to_date')){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.change_in_contract_period'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
			return redirect()->back()->withInput()->withErrors(trans('messages.change_in_contract_period'));
		}

		$salary_types = SalaryType::all();

		$payroll_slip = PayrollSlip::firstOrCreate(array(
				'user_id' => $request->input('user_id'), 
				'from_date' => $request->input('from_date'),
				'to_date' => $request->input('to_date')
				));
		$payroll_slip->user_id = $request->input('user_id');
		$payroll_slip->from_date = $request->input('from_date');
		$payroll_slip->to_date = $request->input('to_date');
		$payroll_slip->hourly_payroll = ($request->has('hourly_payroll')) ? 1 : 0;
		$payroll_slip->hourly = ($request->has('hourly_payroll')) ? $request->input('hourly') : 0;
		$payroll_slip->late = (!$request->has('hourly_payroll')) ? $request->input('late') : 0;
		$payroll_slip->overtime = (!$request->has('hourly_payroll')) ? $request->input('overtime') : 0;
		$payroll_slip->early_leaving = (!$request->has('hourly_payroll')) ? $request->input('early_leaving') : 0;

		if($request->has('employee_contribution'))
	    $payroll_slip->employee_contribution = $request->input('employee_contribution');
		if($request->has('employer_contribution'))
	    $payroll_slip->employer_contribution = $request->input('employer_contribution');
		if($request->has('date_of_contribution'))
	    $payroll_slip->date_of_contribution = $request->input('date_of_contribution') ? : null;
		$payroll_slip->save();

		foreach($salary_types as $salary_type){
			$salary = Payroll::firstOrCreate(array(
				'payroll_slip_id' => $payroll_slip->id,
				'salary_type_id' => $salary_type->id
				));
			$salary->payroll_slip_id = $payroll_slip->id;
			$salary->salary_type_id = $salary_type->id;
			$salary->amount = (!$request->has('hourly_payroll')) ? $request->input($salary_type->id) : 0;
			$salary->save();
		}
		$data = $request->all();
		Helper::storeCustomField($this->form,$payroll_slip->id, $data);

	    $this->logActivity(['module' => 'payroll','unique_id' => $payroll_slip->id,'activity' => 'activity_generated']);
	    
		if($request->has('ajax_submit')){
		  	$response = ['message' => trans('messages.payroll').' '.trans('messages.saved'), 'status' => 'success']; 
		  	return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		}
		return redirect('/payroll/'.$payroll_slip->id)->withSuccess(trans('messages.payroll').' '.trans('messages.saved'));
	}

	public function destroy($payroll_slip_id,Request $request){
	    if(!Entrust::can('generate_payroll')){
	      if($request->has('ajax_submit')){
	          $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	          return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	      }
	      return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
	    }

	    $payroll_slip = PayrollSlip::find($payroll_slip_id);

	    if(!$payroll_slip){
	      if($request->has('ajax_submit')){
	          $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	          return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	      }
	      return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
	    }

	    $this->logActivity(['module' => 'payroll','unique_id' => $payroll_slip->id,'activity' => 'activity_deleted']);
		Helper::deleteCustomField($this->form, $payroll_slip->id);
	    $payroll_slip->delete();

	    if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.payroll').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }
	    return redirect('/payroll')->withSuccess(trans('messages.payroll').' '.trans('messages.deleted'));
	}
}
?>