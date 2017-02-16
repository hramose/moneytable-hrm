<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\LeaveRequest;
use App\Http\Requests\LeaveStatusRequest;
use DB;
use Entrust;
use App\Leave;
use App\LeaveType;
use App\User;
use Auth;
use App\Classes\Helper;

Class LeaveController extends Controller{
    use BasicController;

	protected $form = 'leave-form';

	public function index(Leave $leave){

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.employee'),
        		trans('messages.leave_type'),
        		trans('messages.request').' '.trans('messages.duration'),
        		trans('messages.approved').' '.trans('messages.duration'),
        		trans('messages.status')
        		);

        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $leave_types = LeaveType::pluck('name','id')->all();
        $menu = ['leave'];
        $table_info = array(
			'source' => 'leave',
			'title' => 'Leave List',
			'id' => 'leave_table'
		);

		return view('leave.index',compact('col_heads','menu','table_info','leave_types'));
	}

	public function lists(Request $request){

		$leave_status_details = \App\LeaveStatusDetail::whereDesignationId(Auth::user()->designation_id)->get()->pluck('leave_id')->all();

		if(Entrust::can('manage_all_leave'))
        	$leaves = Leave::all();
        elseif(Entrust::can('manage_subordinate_leave')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
			array_push($child_users, Auth::user()->id);
			$subordinate_leaves = Leave::whereIn('user_id',$child_users)->get()->pluck('id')->all();
			$all_leaves = array_unique(array_merge($leave_status_details,$subordinate_leaves));
    		$leaves = Leave::whereIn('id',$all_leaves)->get();
        }
    	else{
    		$my_leaves = Leave::where('user_id','=',Auth::user()->id)->get()->pluck('id')->all();
			$all_leaves = array_unique(array_merge($leave_status_details,$my_leaves));
			$leaves = Leave::whereIn('id',$all_leaves)->get();
    	}
        
        $rows=array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($leaves as $leave){

        	if($leave->from_date == $leave->to_date)
        		$leave_duration = showDate($leave->from_date);
        	else
        		$leave_duration = showDate($leave->from_date).' '.trans('messages.to').' '.showDate($leave->to_date);

        	$days_count = dateDiff($leave->from_date,$leave->to_date);
        	if($leave->approved_date && count(explode(',', $leave->approved_date)) != $days_count){
        		$approved_dates = explode(',',$leave->approved_date);
        		$leave_approved = '<ol>';
        		foreach($approved_dates as $approved_date)
        			$leave_approved .= '<li>'.showDate($approved_date).'</li>';
        		$leave_approved .= '</ol>';
        	} elseif($leave->approved_date && count(explode(',', $leave->approved_date)) == $days_count)
        		$leave_approved = $leave_duration;
        	else
        		$leave_approved = '';

			$row = array(
					'<div class="btn-group btn-group-xs">'.
					'<a href="/leave/'.$leave->id.'" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-right"></i></a> '.
					((Entrust::can('edit_leave') && $this->leaveAccessible($leave)) ? '<a href="#" data-href="/leave/'.$leave->id.'/edit" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
					((Entrust::can('delete_leave') && $this->leaveAccessible($leave)) ? delete_form(['leave.destroy',$leave->id]) : '').'</div>',
					$leave->User->full_name_with_designation,
					$leave->LeaveType->name,
					$leave_duration,
					$leave_approved,
					trans('messages.'.$leave->status)
					);	
			$id = $leave->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
        	$rows[] = $row;
        }

        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function analysis(){
        $locations = \App\Location::all()->pluck('name','id')->all();
		return view('leave.analysis',compact('locations'));
	}

	public function postAnalysis(Request $request){
        $locations = \App\Location::all()->pluck('name','id')->all();
        $location = ($request->has('location_id')) ? \App\Location::whereId($request->input('location_id'))->first() : null;

        if(defaultRole())
			$users = User::all();
		elseif(Entrust::can('manage_all_employee'))
			$users = User::whereIsHidden(0)->get();
		elseif(Entrust::can('manage_subordinate_employee')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
			array_push($child_users, Auth::user()->id);
			$users = User::whereIn('id',$child_users)->get();
		} else 
			$users = User::whereId(Auth::user()->id)->get();

		$l_types = \App\LeaveType::all();
		$leave_graph = array();
		foreach($users as $user){
			$contract = Helper::getContract($user->id);

			if($contract && (!$location || $location->name == Helper::getLocation(date('Y-m-d'),$user->id))){
				foreach($l_types as $leave_type){
					$leave_graph[$leave_type->name][] = array(
							$user->full_name_with_designation,
							($contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->count()) ? $contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->first()->leave_used : 0,
							($contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->count()) ? $contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->first()->leave_count : 0
						);
				}
			}
		}
        $leave_types = LeaveType::pluck('name','id')->all();
        $assets = ['graph'];

		return view('leave.analysis',compact('leave_graph','leave_types','assets','locations','request'));
	}

	public function leaveStatistics(){

        $col_heads = array(
        		trans('messages.employee'),
        		trans('messages.designation'),
        		trans('messages.contract').' '.trans('messages.duration'),
        		);

		$leave_types = \App\LeaveType::all();
		foreach($leave_types as $leave_type)
			array_push($col_heads,$leave_type->name);

        $menu = ['leave'];
        $table_info = array(
			'source' => 'leave-statistics',
			'title' => 'All Leave List',
			'id' => 'leave_statistics_table'
		);

		return view('leave.statistics',compact('col_heads','menu','table_info'));
	}

	public function postLeaveStatistics(Request $request){

		if(defaultRole())
          $users = User::all();
        elseif(Entrust::can('manage_all_employee'))
          $users = User::whereIsHidden(0)->get();
        elseif(Entrust::can('manage_subordinate_employee')){
          $childs = Helper::childDesignation(Auth::user()->designation_id,1);
		  $child_users = User::whereIn('designation_id',$childs)->pluck('id')->all();
          array_push($child_users, Auth::user()->id);
          $users = User::whereIn('id',$child_users)->get();
        } else
          $users = User::whereId(Auth::user()->id)->get();
		$leave_types = \App\LeaveType::all();

        $rows=array();

        foreach($users as $user){

			$contract = Helper::getContract($user->id);
        	$row = array(
        		$user->full_name,
        		$user->Designation->full_designation,
        		($contract) ? showDate($contract->from_date).' '.trans('messages.to').' '.showDate($contract->to_date) : '-'
        	);

	      	$user_leaves = \App\Leave::whereUserId($user->id)->get();
	      	if($contract){
				foreach($leave_types as $leave_type){
					$used = ($contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->count()) ? $contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->first()->leave_used : 0;
					$allotted = ($contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->count()) ? $contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->first()->leave_count : 0;

					array_push($row,$used.'/'.$allotted);
				}
	      	} else {
				foreach($leave_types as $leave_type)
				array_push($row,'-/-');
	      	}
			$rows[] = $row;
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function show(Leave $leave){

		$leave_status_detail = \App\LeaveStatusDetail::whereLeaveId($leave->id)->whereDesignationId(Auth::user()->designation_id)->first();

		if(!$this->leaveAccessible($leave) && !$leave_status_detail)
          	return redirect('/leave')->withErrors(trans('messages.permission_denied'));

    	$other_leaves = Leave::where('id','!=',$leave->id)
    		->where('user_id','=',$leave->user_id)
    		->get();

    	$status = Helper::translateList(config('lists.leave_status'));
    	$f_date = $leave->from_date;
        $t_date = $leave->to_date;
        while ($f_date <= $t_date) {
            $available_date[] = $f_date;
            $f_date = date ("Y-m-d", strtotime("+1 days", strtotime($f_date)));
        }

        $leave_status_enabled = $this->getLeaveStatus($leave);

        $menu = ['leave'];

		return view('leave.show',compact('leave','other_leaves','status','menu','available_date','leave_status_enabled','leave_status_detail'));
	}

	public function getLeaveStatus($leave){

        $leave_status_detail = \App\LeaveStatusDetail::whereLeaveId($leave->id)->whereDesignationId(Auth::user()->designation_id)->first();

        if($leave_status_detail)
        	$previous_leave_status_detail = \App\LeaveStatusDetail::whereLeaveId($leave->id)->where('id','<',$leave_status_detail->id)->orderBy('id','desc')->first();
        
        if($leave_status_detail && $previous_leave_status_detail)
        	$previous_leave_status = $previous_leave_status_detail->status;
        else
        	$previous_leave_status = null;

        if($leave_status_detail)
        	$next_leave_status_detail = \App\LeaveStatusDetail::whereLeaveId($leave->id)->where('id','>',$leave_status_detail->id)->first();
        
        if($leave_status_detail && $next_leave_status_detail)
        	$next_leave_status = $next_leave_status_detail->status;
        else
        	$next_leave_status = null;

		$last_leave_status_detail = \App\LeaveStatusDetail::whereLeaveId($leave->id)->orderBy('id','desc')->first();

		if(!$leave_status_detail)
			$leave_status_enabled = 0;
		elseif($previous_leave_status == 'rejected' || $previous_leave_status == 'pending')
			$leave_status_enabled = 0;
		elseif($last_leave_status_detail && $last_leave_status_detail->designation_id == Auth::user()->designation_id)
			$leave_status_enabled = 1;
		elseif($next_leave_status == 'pending' || $next_leave_status == null)
			$leave_status_enabled = 1;
		elseif($leave_status_detail == 'pending' || $leave_status_detail == null)
			$leave_status_enabled = 1;
		else
			$leave_status_enabled = 0;

		return $leave_status_enabled;
	}

	public function create(){

		if(!Entrust::can('request_leave'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        $leave_types = LeaveType::pluck('name','id')->all();
        $menu = ['leave'];
        
		return view('leave.create',compact('leave_types','leave'));
	}

	public function edit(Leave $leave){

		if(!Entrust::can('edit_leave') || !$this->leaveAccessible($leave))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        $leave_status_detail = \App\LeaveStatusDetail::whereLeaveId($leave->id)->first();

		if($leave_status_detail && $leave_status_detail->status != 'pending')
            return view('common.error',['message' => trans('messages.leave_cannot_edit')]);

        $leave_types = LeaveType::pluck('name','id')->all();
        $custom_field_values = Helper::getCustomFieldValues($this->form,$leave->id);
        $menu = ['leave'];
		
		return view('leave.edit',compact('leave','leave_types','custom_field_values','menu'));
	}

	public function store(LeaveRequest $request, Leave $leave){	

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		if(!Entrust::can('request_leave')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$user_id = Auth::user()->id;
		$from_date = $request->input('from_date');
		$to_date = $request->input('to_date');

		$contract = \App\Contract::whereUserId($user_id)
			->where('from_date','<=',$from_date)
			->where('to_date','>=',$to_date)
			->first();

		if(!$contract){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.contract_period_not_found'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.contract_period_not_found'));
		}

		$user_leave = \App\UserLeave::whereContractId($contract->id)
			->whereLeaveTypeId($request->input('leave_type_id'))
			->first();

		if(!$user_leave){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.leave_not_defined'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.leave_not_defined'));
		}

		$leave_request_count = (strtotime($to_date) - strtotime($from_date)) / (60*60*24) + 1;
		$leave_type = LeaveType::find($request->input('leave_type_id'));

		$leave_balance = $user_leave->leave_count - $user_leave->leave_used;
		if($leave_balance <  $leave_request_count){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.only').' '.$leave_balance.' '.$leave_type->name.' '.trans('messages.remaining'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.only').' '.$leave_balance.' '.$leave_type->name.' '.trans('messages.remaining'));
		}

		$leaves = Leave::where('user_id','=',$user_id)
			->where(function ($query) use($from_date,$to_date) { $query->where(function ($query) use($from_date,$to_date){
				$query->where('from_date','>=',$from_date)
				->where('from_date','<=',$to_date);
			})->orWhere(function ($query)  use($from_date,$to_date) {
				$query->where('to_date','>=',$from_date)
					->where('to_date','<=',$to_date);
			});})->count();

		if($leaves){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.leave_requested_for_this_period'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withInput()->withErrors(trans('messages.leave_requested_for_this_period'));
		}

		$parents = Helper::getParent(Auth::user()->designation_id);

		if(config('config.leave_approval_level') != 'designation' && !count($parents)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.leave_approver_unavailable'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withInput()->withErrors(trans('messages.leave_approver_unavailable'));
		}

		$data = $request->all();
		$data['user_id'] = Auth::user()->id;
	    $leave->fill($data);
	    $leave->status = 'pending';
		$leave->save();

		if(config('config.leave_approval_level') == 'designation')
			$leave_status_insert[] = array('leave_id' => $leave->id,'designation_id' => config('config.leave_approval_level_detail'),'status' => 'pending');

		if(count($parents)){
			if(config('config.leave_approval_level') == 'single')
				$leave_status_insert[] = array('leave_id' => $leave->id,'designation_id' => $parents[0],'status' => 'pending');
			elseif(config('config.leave_approval_level') == 'multiple'){
				$i = 1;
				foreach($parents as $parent){
					if($i <= config('config.leave_approval_level_multiple') && $parent != null)
					$leave_status_insert[] = array('leave_id' => $leave->id,'designation_id' => $parent,'status' => (($i == 1) ? 'pending' : null));
					$i++;
				}
			}
			elseif(config('config.leave_approval_level') == 'last'){
				$i = 1;
				foreach($parents as $parent){
					$leave_status_insert[] = array('leave_id' => $leave->id,'designation_id' => $parent,'status' => (($i == 1) ? 'pending' : null));
					$i++;
				}
			}
		}

		\App\LeaveStatusDetail::insert($leave_status_insert);

		$this->logActivity(['module' => 'leave_request','unique_id' => $leave->id,'activity' => 'activity_added']);

		Helper::storeCustomField($this->form,$leave->id, $data);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.leave').' '.trans('messages.requested'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.leave').' '.trans('messages.requested'));		
	}

	public function update(LeaveRequest $request, Leave $leave){

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }
        
		if(!Entrust::can('edit_leave') || !$this->leaveAccessible($leave)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
          	return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $leave_status_detail = \App\LeaveStatusDetail::whereLeaveId($leave->id)->first();

		if($leave_status_detail && $leave_status_detail->status != 'pending'){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.leave_cannot_edit'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('leave')->withErrors(trans('messages.leave_cannot_edit'));
		}
		
		$from_date = $request->input('from_date');
		$to_date = $request->input('to_date');

		$contract = \App\Contract::whereUserId($leave->user_id)
			->where('from_date','<=',$from_date)
			->where('to_date','>=',$to_date)
			->first();

		if(!$contract){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.contract_period_not_found'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.contract_period_not_found'));
		}

		$user_leave = \App\UserLeave::whereContractId($contract->id)
			->whereLeaveTypeId($request->input('leave_type_id'))
			->first();

		if(!$user_leave){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.leave_not_defined'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.leave_not_defined'));
		}

		$leave_request_count = (strtotime($to_date) - strtotime($from_date)) / (60*60*24) + 1;
		$leave_type = LeaveType::find($request->input('leave_type_id'));

		$leave_balance = $user_leave->leave_count - $user_leave->leave_used;
		if($leave_balance <  $leave_request_count){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.only').' '.$leave_balance.' '.$leave_type->name.' '.trans('messages.remaining'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.only').' '.$leave_balance.' '.$leave_type->name.' '.trans('messages.remaining'));
		}

		$leaves = Leave::where('id','!=',$leave->id)
			->where('user_id','=',$leave->user_id)
			->where(function ($query) use($from_date,$to_date) { $query->where(function ($query) use($from_date,$to_date)  {
				$query->where('from_date','>=',$from_date)
				->where('from_date','<=',$to_date);
			})->orWhere(function ($query) use($from_date,$to_date)  {
				$query->where('to_date','>=',$from_date)
					->where('to_date','<=',$to_date);
			});})->count();

		if($leaves)
			return redirect()->back()->withErrors(trans('messages.leave_requested_for_this_period'));
		
		$data = $request->all();
		$leave->fill($data);
		$leave->save();
		Helper::updateCustomField($this->form,$leave->id, $data);
		$this->logActivity(['module' => 'leave_request','unique_id' => $leave->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.leave').' '.trans('messages.request').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/leave')->withSuccess(trans('messages.leave').' '.trans('messages.request').' '.trans('messages.updated'));
	}

	public function updateStatus(LeaveStatusRequest $request,$id){

		$leave = Leave::find($id);

		if(!$leave){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/leave')->withErrors(trans('messages.invalid_link'));
		}

		$leave_status_detail = \App\LeaveStatusDetail::whereLeaveId($leave->id)->whereDesignationId(Auth::user()->designation_id)->first();

		if(!$this->leaveAccessible($leave) && !$leave_status_detail)
          	return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		if(!Entrust::can('update_leave_status')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
          	return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $leave_status_enabled = $this->getLeaveStatus($leave);

		if($leave_status_enabled == 0){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
          	return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$contract = \App\Contract::whereUserId($leave->user_id)
			->where('from_date','<=',$leave->from_date)
			->where('to_date','>=',$leave->to_date)
			->first();

		$user_leave = \App\UserLeave::whereContractId($contract->id)
			->whereLeaveTypeId($leave->leave_type_id)
			->first();


    	$f_date = $leave->from_date;
        $t_date = $leave->to_date;
        $request_date = [];
        while ($f_date <= $t_date) {
            $request_date[] = $f_date;
            $f_date = date ("Y-m-d", strtotime("+1 days", strtotime($f_date)));
        }

		$approved_date = $request->has('approved_date') ? explode(',',$request->input('approved_date')) : $request_date;

		if($request->input('status') == 'pending' || $request->input('status') == 'rejected')
			$approved_date = [];

		$leave_type = LeaveType::find($leave->leave_type_id);
		$previously_approved_date = ($leave->approved_date) ? explode(',',$leave->approved_date) : [];

		$adjustable_date = count($approved_date) - count($previously_approved_date);

		$leave_balance = $user_leave->leave_count - $user_leave->leave_used;
		if($adjustable_date > 0 && $leave_balance < $adjustable_date && $request->input('status') == 'approved'){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.only').' '.$leave_balance.' '.$leave_type->name.' '.trans('messages.remaining'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.only').' '.$leave_balance.' '.$leave_type->name.' '.trans('messages.remaining'));
		}

		$leave_status_detail->status = $request->input('status');
		$leave_status_detail->remarks = $request->input('admin_remarks');
		$leave_status_detail->approved_date = count($approved_date) ? implode(',',$approved_date) : null;
		$leave_status_detail->save();

        $next_leave_status_detail = \App\LeaveStatusDetail::whereLeaveId($leave->id)->where('id','>',$leave_status_detail->id)->first();
        if($next_leave_status_detail){
        	$next_leave_status_detail->status = ($request->input('status') == 'pending') ? null : 'pending';
        	$next_leave_status_detail->save();
        }
		$last_leave_status_detail = \App\LeaveStatusDetail::whereLeaveId($leave->id)->orderBy('id','desc')->first();

        if($request->input('status') == 'rejected'){
        	$leave->status = 'rejected';
        	$leave->admin_remarks = $request->input('admin_remarks');
        	$leave->approved_date = null;
        	$leave->save();
        	\App\LeaveStatusDetail::where('id','>',$leave_status_detail->id)->update(['status' => null]);
        } else {
        	$leave->status = 'pending';
        	$leave->admin_remarks = null;
        	$leave->approved_date = null;
        	$leave->save();
        }

		if($last_leave_status_detail && $last_leave_status_detail->designation_id == Auth::user()->designation_id){
			if($request->input('status') == 'approved')
				$user_leave->increment('leave_used',$adjustable_date);
			else
				$user_leave->decrement('leave_used',count($previously_approved_date));

			$leave->status = ($request->input('status')) ? : 'pending';
			$leave->admin_remarks = ($request->input('status') != 'pending') ? $request->input('admin_remarks') : null;
			$leave->approved_date = count($approved_date) ? implode(',',$approved_date) : null;
			$leave->save();
		}
		
		$this->logActivity(['module' => 'leave_request','unique_id' => $leave->id,'activity' => 'activity_status_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.leave').' '.trans('messages.request').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.leave').' '.trans('messages.request').' '.trans('messages.updated'));
	}

	public function destroy(Leave $leave,Request $request){
		if(!Entrust::can('delete_leave') || !$this->leaveAccessible($leave)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
          	return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		if($leave->status != 'pending'){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.leave_cannot_edit'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('leave')->withErrors(trans('messages.leave_cannot_edit'));
		}
		
		Helper::deleteCustomField($this->form, $leave->id);
        $leave->delete();
        $this->logActivity(['module' => 'leave_request','unique_id' => $leave->id,'activity' => 'activity_deleted']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.leave').' '.trans('messages.request').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/leave')->withSuccess(trans('messages.leave').' '.trans('messages.request').' '.trans('messages.deleted'));
	}
}
?>