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

Class PayrollController extends Controller{
    use BasicController;
	protected $form = 'payroll-form';

  public function __construct()
  {
      $this->middleware('officeshift');
  }
    
	public function index(){

    $col_heads = array(
    		trans('messages.option'),
	        trans('messages.slip'),
	        trans('messages.name'),
	        trans('messages.date'),
    		trans('messages.duration'),
    		);

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

        $row = array(
            '<div class="btn-group btn-group-xs">'.
              '<a href="/payroll/'.$payroll_slip->id.'" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-o-right"></i></a>'.
              (Entrust::can('generate_payroll') ? delete_form(['payroll.destroy',$payroll_slip->id]) : '').'</div>',
            $payroll_slip->id,
            $payroll_slip->User->full_name_with_designation,
            date('d M Y',strtotime($payroll_slip->created_at)),
            showDate($payroll_slip->from_date).' '.trans('messages.to').' '.showDate($payroll_slip->to_date),
            );  

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

	public function show($id){

		$payroll_slip = PayrollSlip::find($id);

		if(!$payroll_slip)
			return redirect('/payroll')->withErrors(trans('messages.invalid_link'));

		$user = User::find($payroll_slip->user_id);
		if(!$this->employeeAccessible($user) && $payroll_slip->user_id != Auth::user()->id)
			return redirect('/payroll')->withErrors(trans('messages.invalid_link'));

    	$payroll = Payroll::join('payroll_slip','payroll_slip.id','=','payroll.payroll_slip_id')
    		->where('payroll_slip_id','=',$id)->get()
        	->pluck('amount','salary_type_id')->all();

    	$earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
   	 	$deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();
	    $salaries = Helper::getContract($user->id,$payroll_slip->from_date)->Salary;

		$data = $this->getAttendanceSummary($user,$payroll_slip->from_date,$payroll_slip->to_date);
		$summary = $data['summary'];
		$att_summary = $data['att_summary'];

		return view('payroll.show',compact('payroll_slip','payroll','user','earning_salary_types','deduction_salary_types','summary','att_summary','salaries'));

	}

	public function create(Request $request){

		if(!Entrust::can('generate_payroll'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$from_date = $request->input('from_date') ? : '';
		$to_date = $request->input('to_date') ? : '';
		$user_id = $request->input('user_id') ? : '';

	    if(Entrust::can('manage_all_employee'))
	      $users = \App\User::all()->pluck('full_name_with_designation','id')->all();
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
		$summary = $data['summary'];
		$att_summary = $data['att_summary'];

	  	$no_of_days = dateDiff($to_date,$from_date);
	    $salary_fraction = ($no_of_days) ? ($att_summary['W'] / $no_of_days) : 0;
		
	    $earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
	    $deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();
	    $salaries = Helper::getContract($user->id,$from_date)->Salary;

		return view('payroll.create',compact('users','user','user_id','earning_salary_types','deduction_salary_types','salaries','summary','att_summary','salary_fraction','menu','from_date','to_date'));
	}

	public function getAttendanceSummary($user,$from_date,$to_date){

        $clocks = Clock::where('date','>=',$from_date)->where('date','<=',$to_date)->get();
        $holidays = Holiday::where('date','>=',$from_date)->where('date','<=',$to_date)->get();

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

	  	return ['summary' => $summary,'att_summary' => $att_summary];
	}

	public function generate($action = 'print' , $payroll_slip_id){

		$payroll_slip = PayrollSlip::find($payroll_slip_id);

		if(!$payroll_slip)
			return redirect('/payroll')->withErrors(trans('messages.invalid_link'));

		$user = User::find($payroll_slip->user_id);
		if(!$this->employeeAccessible($user) && $payroll_slip->user_id != Auth::user()->id)
			return redirect('/payroll')->withErrors(trans('messages.invalid_link'));

    	$payroll = Payroll::join('payroll_slip','payroll_slip.id','=','payroll.payroll_slip_id')
    		->where('payroll_slip_id','=',$payroll_slip_id)->get()
        	->pluck('amount','salary_type_id')->all();

    	$earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
   	 	$deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();

   	 	$data = [
   	 		'user' => $user,
	 		'payroll' => $payroll,
   	 		'earning_salary_types' => $earning_salary_types,
   	 		'deduction_salary_types' => $deduction_salary_types,
   	 		'payroll_slip' => $payroll_slip,
   	 		'total_earning' => 0,
   	 		'total_deduction' => 0
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

    	$payroll = Payroll::join('payroll_slip','payroll_slip.id','=','payroll.payroll_slip_id')
    		->where('payroll_slip_id','=',$payroll_slip->id)->get()
        	->pluck('amount','salary_type_id')->all();
	    $earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
	    $deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();
        return view('payroll.edit',compact('payroll_slip','earning_salary_types','deduction_salary_types','payroll'));
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
			$salary->amount = $request->input($salary_type->id);
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
			$salary->amount = $request->input($salary_type->id);
			$salary->save();
		}
		$data = $request->all();
		Helper::storeCustomField($this->form,$payroll_slip->id, $data);

	    $this->logActivity(['module' => 'payroll','unique_id' => $payroll_slip->id,'activity' => 'activity_updated']);
	    
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