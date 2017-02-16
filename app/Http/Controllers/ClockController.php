<?php
namespace App\Http\Controllers;
use DB;
use App\Clock;
use Auth;
use Entrust;
use Config;
use Form;
use Maatwebsite\Excel\Facades\Excel;
use File;
use App\Holiday;
use App\User;
use App\Classes\Helper;
use Illuminate\Http\Request;
use App\Http\Requests\ClockRequest;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\DateWiseAttendanceRequest;
use App\Http\Requests\DateWiseSummaryAttendanceRequest;
use App\Http\Requests\AttendanceUploadRequest;
use Validator;

Class ClockController extends Controller{
    use BasicController;

	public function __construct()
    {
        $this->middleware('officeshift');
    }

	public function in(Request $request){
		if(!$request->has('api') && !Auth::check()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.session_expire'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/');
		}

		$datetime = ($request->input('datetime')) ? : date('Y-m-d H:i:s');	
		$time = date('H:i:s',strtotime($datetime));
		$date = date('Y-m-d',strtotime($datetime));
		$user_id = ($request->input('user_id')) ? : Auth::user()->id;

       	$my_shift = Helper::getShift($date,$user_id);

        if($my_shift->overnight && $time >= '00:00:00' && $time <= $my_shift->out_time){
        	$date = date('Y-m-d',strtotime($date . ' -1 days'));
        	$my_shift = Helper::getShift($date,$user_id);
        }

       	$in_date = $date;
        $in_time = $my_shift->in_time;
        $out_date = date('Y-m-d',strtotime($date . (($my_shift->overnight) ? ' +1 days' : '')));
        $out_time = $out_date.' '.$my_shift->out_time;

		$clocks = Clock::where('user_id','=',$user_id)
			->where('date','=',$date)
			->where(function($query) use($datetime) {
				$query->where('clock_out','=',null)
				->orWhere('clock_out','>=',date('Y-m-d H:i:s',strtotime($datetime)));
			})->count();

		if($clocks){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_clock_in'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        } elseif($request->has('api'))
	        	return response()->json(['type' => 'error','error_code' => '105']);
			return redirect()->back()->withErrors(trans('messages.invalid_clock_in'));
		}

		$clock = new Clock;
		$clock->date = $date;
		$clock->clock_in = date('Y-m-d H:i:s',strtotime($datetime));
		$clock->user_id = $user_id;
		$clock->save();

		$data = array();
		$data['module'] = 'clock';
		$data['activity'] = 'activity_clock_in';
		$data['user_id'] = $user_id;
    	$data['ip'] = \Request::getClientIp();
    	$activity = \App\Activity::create($data);

        if($request->has('ajax_submit')){
        	$data = $this->lists();

        	$clock_button = '<button class="btn btn-success btn-md"><i class="fa fa-arrow-circle-right"></i> '.trans('messages.you_are_clock_in').'</button> '.
        		Form::open(['route' => 'clock.out','role' => 'form', 'class'=>'form-inline','id' => 'clock-out-form','data-table-alter' => 'clock-table','data-clock-button' => 1]).
					'<button type="submit" class="btn btn-danger clock-button">'.trans('messages.clock_out').'</button>'.
					Form::close();

            $response = ['message' => trans('messages.clock_in_successful'), 'status' => 'success','data' => $data,'clock_button' => $clock_button]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        } elseif($request->has('api'))
	        	return response()->json(['type'=>'success']);
		return redirect()->back()->withSuccess(trans('messages.clock_in_successful'));
	}

	public function lists(){
        $clocks = \App\Clock::whereUserId(Auth::user()->id)
            ->where('date','=',date('Y-m-d'))
            ->orderBy('clock_in')
            ->get();

        $data = '';
        foreach($clocks as $clock){
        	$data .= '<tr>
        			<td>'.showTime($clock->clock_in).'</td>
        			<td>'.showTime($clock->clock_out).'</td>
        			</tr>';
        }

        return $data;
	}

	public function out(Request $request){
		if(!$request->has('api') && !Auth::check()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.session_expire'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/');
		}

		$datetime = ($request->input('datetime')) ? : date('Y-m-d H:i:s');	
		$time = date('H:i:s',strtotime($datetime));
		$date = date('Y-m-d',strtotime($datetime));
		$user_id = ($request->input('user_id')) ? : Auth::user()->id;

		$next_date = date('Y-m-d',strtotime($date.' +1 days'));

       	$my_shift = Helper::getShift($date,$user_id);
       	$my_next_date_shift = Helper::getShift($next_date,$user_id);

        if($my_shift->overnight && $time >= '00:00:00' && $datetime < $next_date.' '.$my_next_date_shift->in_time){
        	$date = date('Y-m-d',strtotime($date . ' -1 days'));
        	$my_shift = Helper::getShift($date,$user_id);
        }

		$clock = Clock::where('user_id','=',$user_id)
			->where('date','=',$date)
			->where('clock_out','=',null)
			->first();

		if(!$clock){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.not_clocked_in'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        } elseif($request->has('api'))
	        	return response()->json(['type' => 'error','error_code' => '106']);
			return redirect()->back()->withErrors(trans('messages.not_clocked_in'));
		}

		if($clock->clock_in > date('Y-m-d H:i:s',strtotime($datetime))){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.out_time_not_less_than_in_time'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        } elseif($request->has('api'))
	        	return response()->json(['type' => 'error','error_code' => '107']);
			return redirect()->back()->withErrors(trans('messages.out_time_not_less_than_in_time'));
		}

		$clock->clock_out = date('Y-m-d H:i:s',strtotime($datetime));
		$clock->save();

		$data = array();
		$data['module'] = 'clock';
		$data['activity'] = 'activity_clock_in';
		$data['user_id'] = $user_id;
    	$data['ip'] = \Request::getClientIp();
    	$activity = \App\Activity::create($data);

        if($request->has('ajax_submit')){
        	$data = $this->lists();

        	$clock_button = Form::open(['route' => 'clock.in','role' => 'form', 'class'=>'form-inline','id' => 'clock-in-form','data-table-alter' => 'clock-table','data-clock-button' => 1]).
				'<button type="submit" class="btn btn-success clock-button">'.trans('messages.clock_in').'</button>'.
				Form::close();

            $response = ['message' => trans('messages.clock_out_successful'), 'status' => 'success','data' => $data,'clock_button' => $clock_button]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        } elseif($request->has('api'))
	        	return response()->json(['type' => 'success']);
		return redirect()->back()->withSuccess(trans('messages.clock_out_successful'));
	}

	public function edit(Clock $clock){
		return view('employee.edit_clock',compact('clock'));
	}

	public function update(ClockRequest $request, Clock $clock){

        $validation = Validator::make($request->all(),[
            'clock_in' => 'required'
        ]);

        if($validation->fails()){
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		$clock_in = date('Y-m-d H:i',strtotime($request->input('clock_in')));
		$clock_out = ($request->input('clock_out')) ? date('Y-m-d H:i',strtotime($request->input('clock_out'))) : null;

		if($clock_out && $clock_out < $clock_in)
			return redirect()->back()->withErrors(trans('messages.out_time_not_less_than_in_time'));

		$previous = Clock::where('id','!=',$clock->id)
			->where('date','=',$clock->date)
			->whereUserId($clock->user_id)
			->where('clock_out','<=',$clock->clock_in)
			->orderBy('clock_out','desc')
			->first();


		$next = Clock::where('id','!=',$clock->id)
			->where('date','=',$clock->date)
			->whereUserId($clock->user_id)
			->where('clock_in','>=',$clock->clock_in)
			->orderBy('clock_in','asc')
			->first();

		if($previous && $clock_in < $previous->clock_out)
			return redirect()->back()->withErrors(trans('messages.in_time_cannot_less_than').' '.showTime($previous->clock_out));

		if($next && $clock_in > $next->clock_in)
			return redirect()->back()->withErrors(trans('messages.in_time_cannot_less_than').' '.showTime($next->clock_in));

		if($next && $clock_out == null)
			return redirect()->back()->withErrors(trans('messages.out_time_mandatory'));
		
		if($next && $clock_out && $clock_out > $next->clock_in)
			return redirect()->back()->withErrors(trans('messages.out_time_cannot_greater_than').' '.showTime($next->clock_in));

		$clock->clock_in = $clock_in;
		$clock->clock_out = $clock_out;
		$clock->save();
		return redirect()->back()->withSuccess(trans('messages.saved'));
	}

	public function clock(Request $request,$user_id,$date,$clock_id = null){

		if(config('config.auto_lock_attendance_days') && $date < date('Y-m-d',strtotime(date('Y-m-d') . ' -'.config('config.auto_lock_attendance_days').' days')) && !defaultRole())
			return redirect()->back()->withErrors(trans('messages.attendance_locked'));

		if($date > date('Y-m-d',strtotime(date('Y-m-d') . ' +'.config('config.enable_future_attendance').' days')) && !defaultRole())
			return redirect()->back()->withErrors(trans('messages.future_attendance_disabled'));

        $validation = Validator::make($request->all(),[
            'clock_in' => 'required'
        ]);

        if($validation->fails()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        if($clock_id != null)
        	$clock = Clock::find($clock_id);

        if($clock_id != null && !$clock){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
            return redirect()->back()->withInput()->withErrors(trans('messages.invalid_link'));
        }

		$next_date = date('Y-m-d',strtotime($date.' +1 days'));

        $shift = Helper::getShift($date,$user_id);
       	$next_date_shift = Helper::getShift($next_date);

		$clock_in = date('Y-m-d H:i',strtotime($request->input('clock_in')));
		$clock_out = ($request->input('clock_out')) ? date('Y-m-d H:i',strtotime($request->input('clock_out'))) : null;

		$query1 = Clock::whereUserId($user_id)->where('date','=',$date)->where('clock_in','<=',$clock_in)->where('clock_out','>=',$clock_in);
		if($clock_out)
		$query2 = Clock::whereUserId($user_id)->where('date','=',$date)->where('clock_in','<=',$clock_out)->where('clock_out','>=',$clock_out);

		if($clock_id){
			$query1->where('id','!=',$clock_id);
			if($clock_out)
			$query2->where('id','!=',$clock_id);
		}

		$clock_in_count = $query1->count();
		if($clock_out)
		$clock_out_count = $query2->count();

		if($clock_in < $date)
	        $response = ['message' => trans('messages.clock_in_less_than_current_date'), 'status' => 'error']; 
		elseif(!$shift->overnight && $clock_in >= $next_date)
	        $response = ['message' => trans('messages.clock_in_greater_than_current_date'), 'status' => 'error']; 
		elseif($shift->overnight && $clock_in >= $next_date.' '.$next_date_shift->in_time)
	        $response = ['message' => trans('messages.clock_in_greater_than_current_date_overtime'), 'status' => 'error'];
		elseif($clock_in_count > 0)
	        $response = ['message' => trans('messages.clock_in_between_time'), 'status' => 'error']; 
		elseif($clock_out && $clock_out_count)
	        $response = ['message' => trans('messages.clock_out_between_time'), 'status' => 'error'];
		elseif($clock_out && $clock_in > $clock_out)
	        $response = ['message' => trans('messages.out_time_not_less_than_in_time'), 'status' => 'error'];
    	elseif($clock_out && !$shift->overnight && $clock_out >= $next_date)
	        $response = ['message' => trans('messages.clock_out_greater_than_current_date'), 'status' => 'error'];
    	elseif($clock_out && $shift->overnight && $clock_out >= $next_date.' '.$next_date_shift->in_time)
	        $response = ['message' => trans('messages.clock_out_greater_than_current_date_overtime'), 'status' => 'error'];

    	if(isset($response) && $response['status'] == 'error'){
    		if($request->has('ajax_submit'))
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        else
            	return redirect()->back()->withInput()->withErrors($response['message']);
    	}

    	if($clock_id == null){
	        $clock = new Clock;
	        $clock->date = $date;
        	$clock->user_id = $user_id;
    	}

        $clock->clock_in = $clock_in;
        $clock->clock_out = $clock_out;
        $clock->save();

		if($request->has('ajax_submit')){
			$clocks = Clock::where('date','=',$date)->whereUserId($user_id)->orderBy('clock_in')->get();
			$data = '';
			foreach($clocks as $clock){
				$data .= '<tr>
					<td>'.showDateTime($clock->clock_in).'</td>
					<td>'.showDateTime($clock->clock_out).'</td>
					<td>
						<div class="btn-group btn-group-xs">
					  		<a href="#" data-href="/clock/'.$clock->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i> </a>'.
					  			delete_form(['clock.destroy',$clock->id]).
					  	'</div>
					</td>
				</tr>';
			}
            $response = ['message' => trans('messages.saved'), 'status' => 'success','data' => $data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.saved'));
	}

	public function attendance(){

		$date = date('Y-m-d');

        $col_heads = array(
        		trans('messages.status'),
        		trans('messages.employee'),
        		trans('messages.clock_in'),
        		trans('messages.clock_out'),
        		trans('messages.late'),
        		trans('messages.early_leaving'),
        		trans('messages.overtime'),
        		trans('messages.total_work'),
        		trans('messages.total_rest'),
        		trans('messages.remarks'));

        $menu = ['attendance','daily_attendance'];
        $assets = ['graph'];

        $table_info = array(
			'source' => 'daily-attendance',
			'title' => 'Daily Attendance',
			'id' => 'daily_attendance_table',
			'form' => 'daily_attendance'
		);

		return view('employee.attendance',compact('col_heads','date','menu','table_info','assets'));
	}

	public function postAttendance(Request $request){
        $response = ['message' => trans('messages.request_submit'), 'status' => 'success']; 
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	}

	public function listDailyAttendance(Request $request){

		$date = ($request->input('date')) ? : date('Y-m-d');

		if(Entrust::can('manage_all_employee'))
			$users = User::all();
		elseif(Entrust::can('manage_subordinate_employee')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
			array_push($child_users, Auth::user()->id);
			$users = User::whereIn('id',$child_users)->get();
		} else 
			$users = User::whereId(Auth::user()->id)->get();

        $rows=array();
        $cols_summary=array();
        $raw_data = array();
        $clocked_user = array();

        $holiday = Holiday::where('date','=',$date)->count();

        $total_late = 0;
        $total_early = 0;
        $total_overtime = 0;
        $total_working = 0;
        $total_rest = 0;
		
        $clocks = Clock::where('date','=',$date)->get();

		$leaves = \App\Leave::whereStatus('approved')->where(function($query) use($date){
			$query->where('from_date','>=',$date)
			->orWhere('to_date','<=',$date)
			->orWhere(function($query1) use($date){
				$query1->where('from_date','<',$date)
				->where('to_date','>',$date);
			});
		})->get();

		$raw_data = array();

        foreach($users as $user){
        	$tag = '';
        	$late = 0;
        	$early = 0;
        	$working = 0;
        	$overtime = 0;
        	$rest = 0;
        	$my_shift = Helper::getShift($date,$user->id);

        	$user_leaves = $leaves->whereLoose('user_id',$user->id)->all();

	        $leave_approved = array();
	        foreach($user_leaves as $user_leave){
	            $leave_approved_dates = ($user_leave->approved_date) ? explode(',',$user_leave->approved_date) : [];
	            foreach($leave_approved_dates as $leave_approved_date)
	                $leave_approved[] = $leave_approved_date;
	        }

        	$my_shift->in_time = $date.' '.$my_shift->in_time;
        	if($my_shift->overnight)
        		$my_shift->out_time = date('Y-m-d',strtotime($date . ' +1 days')).' '.$my_shift->out_time;
        	else
        		$my_shift->out_time = $date.' '.$my_shift->out_time;

        	$out = $clocks->whereLoose('date',$date)->whereLoose('user_id',$user->id)->sortBy('clock_in')->last();
        	$in = $clocks->whereLoose('date',$date)->whereLoose('user_id',$user->id)->sortBy('clock_in')->first();
			$records = $clocks->whereLoose('date',$date)->whereLoose('user_id',$user->id)->all();

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
			
			unset($leave_approved);

			$late = (isset($in) && (strtotime($in->clock_in) > strtotime($my_shift->in_time)) && $my_shift->in_time != $my_shift->out_time) ? abs(strtotime($my_shift->in_time) - strtotime($in->clock_in)) : 0;

			if($late)
				$tag .= Helper::getAttendanceTag('late');

			$total_late += $late;
			$early = (isset($out) && $out->clock_out != null && (strtotime($out->clock_out) < strtotime($my_shift->out_time)) && $my_shift->in_time != $my_shift->out_time) ? abs(strtotime($my_shift->out_time) - strtotime($out->clock_out)) : 0;

			if($early)
				$tag .= Helper::getAttendanceTag('early');

			$total_early += $early;

			foreach($records as $record){
				if($record->clock_in >= $my_shift->out_time && $record->clock_out != null)
					$overtime += strtotime($record->clock_out) - strtotime($record->clock_in);
				elseif($record->clock_in < $my_shift->out_time && $record->clock_out > $my_shift->out_time)
					$overtime += strtotime($record->clock_out) - strtotime($my_shift->out_time);
			}

			if($overtime)
				$tag .= Helper::getAttendanceTag('overtime');

			$total_overtime += $overtime;

			foreach($records as $record)
				$working += ($record->clock_out != null) ? abs(strtotime($record->clock_out) - strtotime($record->clock_in)) : 0;
			$total_working += $working;

			$rest = (isset($in) && $out->clock_out != null) ? (abs(strtotime($out->clock_out) - strtotime($in->clock_in)) - $working) : 0;

			$total_rest += $rest;

			$raw_data[] = array(
					'name' => $user->full_name,
					'late' => ($late) ? $late/60 : 0,
					'late_tootltip' => $user->full_name_with_designation.' late for '.Helper::showDetailDuration($late),
					'early' => ($early) ? $early/60 : 0,
					'early_tootltip' => $user->full_name_with_designation.' left office before '.Helper::showDetailDuration($early),
					'overtime' => ($overtime) ? $overtime/60 : 0,
					'overtime_tootltip' => $user->full_name_with_designation.' worked overtime for '.Helper::showDetailDuration($overtime),
					'working' => ($working) ? $working/60 : 0,
					'working_tootltip' => $user->full_name_with_designation.' worked for '.Helper::showDetailDuration($working),
					'rest' => ($rest) ? $rest/60 : 0,
					'rest_tootltip' => $user->full_name_with_designation.' took rest for '.Helper::showDetailDuration($rest),
				);

			$rows[] = array(
				$attendance_label,
				$user->full_name_with_designation,
				(isset($in)) ? showTime($in->clock_in) : '-',
				(isset($out)) ? showTime($out->clock_out) : '-',
				Helper::showDuration($late),
				Helper::showDuration($early),
				Helper::showDuration($overtime),
				Helper::showDuration($working),
				Helper::showDuration($rest),
				$tag
				);

			$cols_summary[$user->id] = $attendance;
			unset($tag);
        }

        $graph_late = array();
        $graph_early = array();
        $graph_overtime = array();
        $graph_working = array();
        $graph_rest = array();
        foreach($raw_data as $data){
        	$graph_late[] = array($data['name'],$data['late'],$data['late_tootltip']);
        	$graph_early[] = array($data['name'],$data['early'],$data['early_tootltip']);
        	$graph_overtime[] = array($data['name'],$data['overtime'],$data['overtime_tootltip']);
        	$graph_working[] = array($data['name'],$data['working'],$data['working_tootltip']);
        	$graph_rest[] = array($data['name'],$data['rest'],$data['rest_tootltip']);
        }

        $cols_summary = array_count_values($cols_summary);

        $list['aaData'] = $rows;

        $list['foot'] = '<tr>
			<th colspan="4"></th>
			<th>'.Helper::showDuration($total_late).'</th>
			<th>'.Helper::showDuration($total_early).'</th>
			<th>'.Helper::showDuration($total_overtime).'</th>
			<th>'.Helper::showDuration($total_working).'</th>
			<th>'.Helper::showDuration($total_rest).'</th>
			<th></th>
		</tr>';

		$list['graph']['late'] = $graph_late;
		$list['graph']['early'] = $graph_early;
		$list['graph']['overtime'] = $graph_overtime;
		$list['graph']['working'] = $graph_working;
		$list['graph']['rest'] = $graph_rest;

		$list['title']['late'] = 'Late Record for all employee on '.showDate($date);
		$list['title']['early'] = 'Early Leaving Record for all employee on '.showDate($date);
		$list['title']['overtime'] = 'Overtime Record for all employee on '.showDate($date);
		$list['title']['working'] = 'Working Record for all employee on '.showDate($date);
		$list['title']['rest'] = 'Rest Record for all employee on '.showDate($date);

		$list['summary'] = '<ul class="list-group">
					  <li class="list-group-item">
						<span class="badge badge-danger">'.(array_key_exists('A',$cols_summary) ? $cols_summary['A'] : '-').'</span>'.trans('messages.absent').
					  '</li>
					  <li class="list-group-item">
						<span class="badge badge-info">'.(array_key_exists('H',$cols_summary) ? $cols_summary['H'] : '-').'</span>'.trans('messages.holiday').
					  '</li>
					  <li class="list-group-item">
						<span class="badge badge-success">'.(array_key_exists('P',$cols_summary) ? $cols_summary['P'] : '-').'</span>'.trans('messages.present').
					  '</li>
					  <li class="list-group-item">
						<span class="badge badge-warning">'.(array_key_exists('L',$cols_summary) ? $cols_summary['L'] : '-').'</span>'.trans('messages.leave').
					  '</li>
					</ul>';

        return json_encode($list);
	}

	public function dateWiseAttendance(){

		if(Entrust::can('manage_all_employee'))
			$users = User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_employee')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
			array_push($child_users, Auth::user()->id);
			$users = User::whereIn('id',$child_users)->get()->pluck('full_name_with_designation','id')->all();
		} else 
			$users = User::whereId(Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();

        $col_heads = array(
        		trans('messages.status'),
        		trans('messages.date'),
        		trans('messages.clock_in'),
        		trans('messages.clock_out'),
        		trans('messages.late'),
        		trans('messages.early_leaving'),
        		trans('messages.overtime'),
        		trans('messages.total_work'),
        		trans('messages.total_rest'),
        		trans('messages.remarks')
        		);
        $menu = ['attendance','date_wise_attendance'];
        $assets = ['graph'];
        $table_info = array(
			'source' => 'date-wise-attendance',
			'title' => 'Date wise Attendance',
			'id' => 'date_wise_attendance_table',
			'form' => 'date_wise_attendance'
		);

		return view('employee.date_wise_attendance',compact('col_heads','users','menu','table_info','assets'));
	}

	public function postDateWiseAttendance(DateWiseAttendanceRequest $request){
        $response = ['message' => trans('messages.request_submit'), 'status' => 'success']; 
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	}

	public function listDateWiseAttendance(DateWiseAttendanceRequest $request){

		$user_id = $request->input('user_id') ? : Auth::user()->id;
		$from_date = $request->input('from_date') ? : date('Y-m-d');
		$to_date = $request->input('to_date') ? : date('Y-m-d');

        $rows=array();
        $ajax_data = '';
        $cols_summary=array();
        $raw_data = array();

		$leave_approved = array();

        $leaves = \App\Leave::whereUserId($user_id)->whereStatus('approved')->where(function($query) use($from_date,$to_date) {
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

        $user = User::find($user_id);
        $clocked_user = array();

        $clocks = Clock::where('date','>=',$from_date)->where('date','<=',$to_date)->get();

        $holidays = Holiday::where('date','>=',$from_date)->where('date','<=',$to_date)->get();

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

			$raw_data[] = array(
					'date' => showDate($date),
					'late' => ($late) ? $late/60 : 0,
					'late_tootltip' => $user->full_name_with_designation.' late for '.Helper::showDetailDuration($late),
					'early' => ($early) ? $early/60 : 0,
					'early_tootltip' => $user->full_name_with_designation.' left office before '.Helper::showDetailDuration($early),
					'overtime' => ($overtime) ? $overtime/60 : 0,
					'overtime_tootltip' => $user->full_name_with_designation.' worked overtime for '.Helper::showDetailDuration($overtime),
					'working' => ($working) ? $working/60 : 0,
					'working_tootltip' => $user->full_name_with_designation.' worked for '.Helper::showDetailDuration($working),
					'rest' => ($rest) ? $rest/60 : 0,
					'rest_tootltip' => $user->full_name_with_designation.' took rest for '.Helper::showDetailDuration($rest),
				);

			$rows[] = array(
					$attendance_label,
					showDate($date),
					(isset($in)) ? showTime($in->clock_in) : '-',
					(isset($out)) ? showTime($out->clock_out) : '-',
					Helper::showDuration($late),
					Helper::showDuration($early),
					Helper::showDuration($overtime),
					Helper::showDuration($working),
					Helper::showDuration($rest),
					$tag
				);

			$cols_summary[$date] = $attendance;
			$date = date('Y-m-d',strtotime($date . ' +1 days'));
        }

        $graph_late = array();
        $graph_early = array();
        $graph_overtime = array();
        $graph_working = array();
        $graph_rest = array();
        foreach($raw_data as $data){
        	$graph_late[] = array($data['date'],$data['late'],$data['late_tootltip']);
        	$graph_early[] = array($data['date'],$data['early'],$data['early_tootltip']);
        	$graph_overtime[] = array($data['date'],$data['overtime'],$data['overtime_tootltip']);
        	$graph_working[] = array($data['date'],$data['working'],$data['working_tootltip']);
        	$graph_rest[] = array($data['date'],$data['rest'],$data['rest_tootltip']);
        }

		$list['title']['late'] = 'Late Record for '.$user->full_name_with_designation.' from '.showDate($from_date).' to '.showDate($to_date);
		$list['title']['early'] = 'Early Leaving Record for '.$user->full_name_with_designation.' from '.showDate($from_date).' to '.showDate($to_date);
		$list['title']['overtime'] = 'Overtime Record for '.$user->full_name_with_designation.' from '.showDate($from_date).' to '.showDate($to_date);
		$list['title']['working'] = 'Working Record for '.$user->full_name_with_designation.' from '.showDate($from_date).' to '.showDate($to_date);
		$list['title']['rest'] = 'Rest Record for '.$user->full_name_with_designation.' from '.showDate($from_date).' to '.showDate($to_date);

        $ajax_data = '<tr><th>Late</th><td><span class="badge badge-danger">'.Helper::showDetailDuration($total_late).'</span></td></tr>
				<tr><th>Early</th><td><span class="badge badge-warning">'.Helper::showDetailDuration($total_early).'</span></td></tr>
				<tr><th>Overtime</th><td><span class="badge badge-success">'.Helper::showDetailDuration($total_overtime).'</span></td></tr>
				<tr><th>Working</th><td><span class="badge badge-default">'.Helper::showDetailDuration($total_working).'</span></td></tr>
				<tr><th>Rest</th><td><span class="badge badge-info">'.Helper::showDetailDuration($total_rest).'</span></td></tr>';

        if($request->has('attendance_statistics'))
        	return $ajax_data;
        elseif($request->has('ajax_submit')){
        	$response = ['message' => trans('messages.request_submit'), 'status' => 'success','data' => $ajax_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

		$list['graph']['late'] = $graph_late;
		$list['graph']['early'] = $graph_early;
		$list['graph']['overtime'] = $graph_overtime;
		$list['graph']['working'] = $graph_working;
		$list['graph']['rest'] = $graph_rest;

        $cols_summary = array_count_values($cols_summary);
        $tag_summary = array_count_values($tag_count);
        $list['aaData'] = $rows;
        $list['foot'] = '<tr>
        				<th colspan="4"></th>
        				<th>'.Helper::showDuration($total_late).'</th>
        				<th>'.Helper::showDuration($total_early).'</th>
        				<th>'.Helper::showDuration($total_overtime).'</th>
        				<th>'.Helper::showDuration($total_working).'</th>
        				<th>'.Helper::showDuration($total_rest).'</th>
        				<th></th>
        				</tr>';
       	$list['summary'] = '<ul class="list-group">
				  <li class="list-group-item">
					<span class="badge badge-danger">'.(array_key_exists('A',$cols_summary) ? $cols_summary['A'] : '-').'</span>'.trans('messages.absent').
				  '</li>
				  <li class="list-group-item">
					<span class="badge badge-info">'.(array_key_exists('H',$cols_summary) ? $cols_summary['H'] : '-').'</span>'.trans('messages.holiday').
				  '</li>
				  <li class="list-group-item">
					<span class="badge badge-success">'.(array_key_exists('P',$cols_summary) ? $cols_summary['P'] : '-').'</span>'.trans('messages.present').
				  '</li>
				  <li class="list-group-item">
					<span class="badge badge-warning">'.(array_key_exists('L',$cols_summary) ? $cols_summary['L'] : '-').'</span>'.trans('messages.leave').
				  '</li>
				  <li class="list-group-item">
					<span class="badge badge-danger">'.(array_key_exists('L',$tag_summary) ? $tag_summary['L'] : '-').'</span>'.trans('messages.late').
				  '</li>
				  <li class="list-group-item">
					<span class="badge badge-success">'.(array_key_exists('O',$tag_summary) ? $tag_summary['O'] : '-').'</span>'.trans('messages.overtime').
				  '</li>
				  <li class="list-group-item">
					<span class="badge badge-warning">'.(array_key_exists('E',$tag_summary) ? $tag_summary['E'] : '-').'</span>'.trans('messages.early').
				  '</li>
				</ul>';

        return json_encode($list);
	}

	public function dateWiseSummaryAttendance(){

        $col_heads = array(
        		trans('messages.employee'),
        		trans('messages.late'),
        		trans('messages.early_leaving'),
        		trans('messages.overtime'),
        		trans('messages.total_work'),
        		trans('messages.total_rest'),
        		trans('messages.present'),
        		trans('messages.holiday'),
        		trans('messages.leave'),
        		trans('messages.absent'),
        		trans('messages.late').' '.trans('messages.count'),
        		trans('messages.overtime').' '.trans('messages.count'),
        		trans('messages.early').' '.trans('messages.leaving').' '.trans('messages.count')
        		);
        $menu = ['attendance','date_wise_summary_attendance'];
        $assets = ['graph'];
        $table_info = array(
			'source' => 'date-wise-summary-attendance',
			'title' => 'Date wise Summary Attendance',
			'id' => 'date_wise_summary_attendance_table',
			'form' => 'date_wise_summary_attendance'
		);

		return view('employee.date_wise_summary_attendance',compact('col_heads','assets','menu','table_info'));
	}

	public function postDateWiseSummaryAttendance(DateWiseSummaryAttendanceRequest $request){
        $response = ['message' => trans('messages.request_submit'), 'status' => 'success']; 
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	}

	public function listDateWiseSummaryAttendance(Request $request){

		$from_date = $request->input('from_date') ? : date('Y-m-d');
		$to_date = $request->input('to_date') ? : date('Y-m-d');

        $rows=array();
        $cols_summary=array();
        $raw_data = array();
        $raw_data2 = array();

		if(Entrust::can('manage_all_employee'))
			$users = User::all();
		elseif(Entrust::can('manage_subordinate_employee')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
			array_push($child_users, Auth::user()->id);
			$users = User::whereIn('id',$child_users)->get();
		} else 
			$users = User::whereId(Auth::user()->id)->get();

        $clocks = Clock::where('date','>=',$from_date)->where('date','<=',$to_date)->get();
        $holidays = Holiday::where('date','>=',$from_date)->where('date','<=',$to_date)->get();

        foreach($users as $user)
        {

			$leave_approved = array();
			$leaves = \App\Leave::whereUserId($user->id)->whereStatus('approved')->get();
	        foreach($leaves as $leave){
	            $leave_approved_dates = ($leave->approved_date) ? explode(',',$leave->approved_date) : [];
	            foreach($leave_approved_dates as $leave_approved_date)
	                $leave_approved[] = $leave_approved_date;
	        }

        	$tag_count = array();
	        $total_late = 0;
	        $total_early = 0;
	        $total_overtime = 0;
	        $total_working = 0;
	        $total_rest = 0;
	        $attendance = array();

	        $date = $from_date;
        	while($date <= $to_date){
	        	$working = 0;
	        	$rest = 0;
	        	$late = 0;
	        	$early = 0;
	        	$overtime = 0;
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
				$total_late += $late;

				if($late)
					$tag_count[] = 'L';

				$early = (isset($out) && $out->clock_out != null && (strtotime($out->clock_out) < strtotime($my_shift->out_time)) && $my_shift->in_time != $my_shift->out_time) ? abs(strtotime($my_shift->out_time) - strtotime($out->clock_out)) : 0;
				$total_early += $early;

				if($early)
					$tag_count[] = 'E';

				foreach($records as $record){
					if($record->clock_in >= $my_shift->out_time && $record->clock_out != null)
						$overtime = strtotime($record->clock_out) - strtotime($record->clock_in);
					elseif($record->clock_in < $my_shift->out_time && $record->clock_out > $my_shift->out_time)
						$overtime = strtotime($record->clock_out) - strtotime($my_shift->out_time);
				}
				$total_overtime += $overtime;
				if($overtime)
					$tag_count[] = 'O';

				foreach($records as $record)
					$working += ($record->clock_out != null) ? abs(strtotime($record->clock_out) - strtotime($record->clock_in)) : 0;
				$total_working += $working;

				$rest = (isset($in) && $out->clock_out != null) ? (abs(strtotime($out->clock_out) - strtotime($in->clock_in)) - $working) : 0;
				$total_rest += $rest;

				$holiday = $holidays->whereLoose('date',$date)->first();

				if(isset($in))
					$attendance[] = 'P';
				elseif(count($leave_approved) && in_array($date,$leave_approved))
					$attendance[] = 'L';
				elseif($holiday)
					$attendance[] = 'H';
				elseif(!$holiday && $date < date('Y-m-d'))
					$attendance[] = 'A';
				else
					$attendance[] = '';

				$date = date('Y-m-d',strtotime($date . ' +1 days'));
        	}

			$raw_data[] = array(
					'name' => $user->full_name,
					'late' => ($total_late) ? $total_late/60 : 0,
					'late_tootltip' => $user->full_name_with_designation.' late for '.Helper::showDetailDuration($total_late),
					'early' => ($total_early) ? $total_early/60 : 0,
					'early_tootltip' => $user->full_name_with_designation.' left office before '.Helper::showDetailDuration($total_early),
					'overtime' => ($total_overtime) ? $total_overtime/60 : 0,
					'overtime_tootltip' => $user->full_name_with_designation.' worked overtime for '.Helper::showDetailDuration($total_overtime),
					'working' => ($total_working) ? $total_working/60 : 0,
					'working_tootltip' => $user->full_name_with_designation.' worked for '.Helper::showDetailDuration($total_working),
					'rest' => ($total_rest) ? $total_rest/60 : 0,
					'rest_tootltip' => $user->full_name_with_designation.' took rest for '.Helper::showDetailDuration($total_rest),
				);

			$tag_count = array_count_values($tag_count);
			$attendance = array_count_values($attendance);

			$count_present = (array_key_exists('P', $attendance) ? $attendance['P'] : 0);
			$count_holiday = (array_key_exists('H', $attendance) ? $attendance['H'] : 0);
			$count_leave = (array_key_exists('L', $attendance) ? $attendance['L'] : 0);
			$count_absent = (array_key_exists('A', $attendance) ? $attendance['A'] : 0);
			$count_late = (array_key_exists('L', $tag_count) ? $tag_count['L'] : 0);
			$count_overtime = (array_key_exists('O', $tag_count) ? $tag_count['O'] : 0);
			$count_early = (array_key_exists('E', $tag_count) ? $tag_count['E'] : 0);

			$raw_data2[] = array(
				'name' => $user->full_name,
				'present' => $count_present,
				'holiday' => $count_holiday,
				'leave' => $count_leave,
				'absent' => $count_absent,
				'late' => $count_late,
				'overtime' => $count_overtime,
				'early' => $count_early
				);

			$row = array($user->full_name_with_designation,
					Helper::showDuration($total_late),
					Helper::showDuration($total_early),
					Helper::showDuration($total_overtime),
					Helper::showDuration($total_working),
					Helper::showDuration($total_rest),
					$count_present,
					$count_holiday,
					$count_leave,
					$count_absent,
					$count_late,
					$count_overtime,
					$count_early,
					);
        	$rows[] = $row;
        	unset($tag_count);
        	unset($attendance);
		}

        $graph_late = array();
        $graph_early = array();
        $graph_overtime = array();
        $graph_working = array();
        $graph_rest = array();
        foreach($raw_data as $data){
        	$graph_late[] = array($data['name'],$data['late'],$data['late_tootltip']);
        	$graph_early[] = array($data['name'],$data['early'],$data['early_tootltip']);
        	$graph_overtime[] = array($data['name'],$data['overtime'],$data['overtime_tootltip']);
        	$graph_working[] = array($data['name'],$data['working'],$data['working_tootltip']);
        	$graph_rest[] = array($data['name'],$data['rest'],$data['rest_tootltip']);
        }
        
		$list['title']['late'] = 'Late Record for all employee from '.showDate($from_date).' to '.showDate($to_date);
		$list['title']['early'] = 'Early Leaving Record for all employee from '.showDate($from_date).' to '.showDate($to_date);
		$list['title']['overtime'] = 'Overtime Record for all employee from '.showDate($from_date).' to '.showDate($to_date);
		$list['title']['working'] = 'Working Record for all employee from '.showDate($from_date).' to '.showDate($to_date);
		$list['title']['rest'] = 'Rest Record for all employee from '.showDate($from_date).' to '.showDate($to_date);

		$list['graph']['late'] = $graph_late;
		$list['graph']['early'] = $graph_early;
		$list['graph']['overtime'] = $graph_overtime;
		$list['graph']['working'] = $graph_working;
		$list['graph']['rest'] = $graph_rest;

        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function updateAttendance(Request $request){

		$user_id = ($request->input('user_id')) ? : Auth::user()->id;
		$date = ($request->input('date')) ? : date('Y-m-d');

		if(config('config.auto_lock_attendance_days') && $date < date('Y-m-d',strtotime(date('Y-m-d') . ' -'.config('config.auto_lock_attendance_days').' days')) && !defaultRole())
			return redirect()->back()->withErrors(trans('messages.attendance_locked'));

		if($date > date('Y-m-d',strtotime(date('Y-m-d') . ' +'.config('config.enable_future_attendance').' days')) && !defaultRole())
			return redirect()->back()->withErrors(trans('messages.future_attendance_disabled'));

		$user = User::find($user_id);
		$date_of_leaving = $user->Profile->date_of_leaving;
		$date_of_joining = $user->Profile->date_of_joining;

		if(!$date_of_joining)
			return redirect('/dashboard')->withErrors(trans('messages.set_date_of_joining'));

		if($date_of_joining > $date)
			return redirect()->back()->withErrors(trans('messages.no_entry_before_date_of_joining'));

		if($date_of_leaving && $date_of_leaving < $date)
			return redirect()->back()->withErrors(trans('messages.inactive_employee'));

		$clocks = Clock::where('date','=',$date)
			->whereUserId($user_id)
			->orderBy('clock_in')
			->get();

		if(!Entrust::can('update_attendance'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		if(Entrust::can('manage_all_employee'))
			$users = User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_employee')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
			// array_push($child_users, Auth::user()->id);
			$users = User::whereIn('id',$child_users)->get()->pluck('full_name_with_designation','id')->all();
		} else 
			$users = [];

    	$my_shift = Helper::getShift($date,$user_id);
    	$my_shift->in_time = $date.' '.$my_shift->in_time;
    	if($my_shift->overnight)
    		$my_shift->out_time = date('Y-m-d',strtotime($date . ' +1 days')).' '.$my_shift->out_time;
    	else
    		$my_shift->out_time = $date.' '.$my_shift->out_time;

    	$holiday = \App\Holiday::where('date','=',$date)->first();
    	$label = '<span class="badge badge-success">'.trans('messages.working').' '.trans('messages.day').'</span>';
    	if($holiday)
    		$label = '<span class="badge badge-info">'.trans('messages.holiday').': '.$holiday->description.'</span>';


		$leaves = \App\Leave::whereUserId($user_id)->whereStatus('approved')->where(function($query) use($date){
			$query->where('from_date','>=',$date)
			->orWhere('to_date','<=',$date)
			->orWhere(function($query1) use($date){
				$query1->where('from_date','<',$date)
				->where('to_date','>',$date);
			});
		})->get();
        $leave_approved = array();
        foreach($leaves as $leave){
            $leave_approved_dates = ($leave->approved_date) ? explode(',',$leave->approved_date) : [];
            foreach($leave_approved_dates as $leave_approved_date)
                $leave_approved[] = $leave_approved_date;
        }

    	if(in_array($date,$leave_approved))
    		$label = '<span class="badge badge-danger">'.trans('messages.on').' '.trans('messages.leave').'</span>';

        $assets = ['timepicker'];
        $menu = ['attendance','update_attendance'];
        return view('employee.update_attendance',compact('users','assets','user','date','clocks','my_shift','menu','label'));
	}

	public function uploadAttendance(AttendanceUploadRequest $request){
		
		if(!Entrust::can('upload_attendance'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$filename = uniqid();
		$extension = $request->file('file')->getClientOriginalExtension();
	 	$file = $request->file('file')->move('uploads/attendance',$filename.".".$extension);
	 	$filename_extension = 'uploads/attendance/'.$filename.'.'.$extension;
		$xls_datas = Excel::load($filename_extension, function($reader) { })->toArray();
		if(count($xls_datas) > 0)
		{
			$employees = User::join('profile','profile.user_id','=','users.id')
				->select(DB::raw('users.id AS user_id,employee_code'))
				->pluck('user_id','employee_code')->all();

		    $data = array();
		    foreach($xls_datas as $xls_data)
		    {
		      $employee_code = $xls_data['employee_code'];
		      $user_id = (isset($employees[$employee_code])) ? $employees[$employee_code] : NULL;
		      $date = date('Y-m-d',strtotime($xls_data['date']));
		      $clock_in = date('Y-m-d H:i',strtotime($xls_data['clock_in']));
		      $clock_out = date('Y-m-d H:i',strtotime($xls_data['clock_out']));
		      
		      $clock = Clock::where('user_id','=',$user_id)
		      	->where('date','=',$date)
		      	->where(function ($query) use($clock_in) {
		      		$query->where('clock_out','=',null)
		      		->orWhere('clock_out','>=',$clock_in);
		      		})->count();

		      if($user_id != null && !$clock && strtotime($clock_in) < strtotime($clock_out))
		      $data[] = array(
		      		'user_id' => $user_id,
		      		'date' => $date,
		      		'clock_in' => $clock_in,
		      		'clock_out' => $clock_out,
		      		'created_at' => date('Y-m-d H:i:s'),
		      		'updated_at' => date('Y-m-d H:i:s')
		      		);
		    }
		    if(count($data))
		    	Clock::insert($data);
		}
		if (File::exists($filename_extension))
			File::delete($filename_extension);

		return redirect('/dashboard')->withSuccess(count($data).' '.trans('messages.attendance_upload').' '.trans('messages.out_of').' '.count($xls_datas).' '.trans('messages.attendance'));
	}

	public function shift(Request $request){

        $col_heads = array(
        		trans('messages.employee'),
        		trans('messages.shift_name'),
        		trans('messages.clock_in'),
        		trans('messages.clock_out')
				);

        $date = ($request->input('date')) ? : date('Y-m-d');

        $menu = ['attendance','shift_detail'];
        $table_info = array(
			'source' => 'shift-detail',
			'title' => 'Shift Detail',
			'id' => 'shift_detail_table',
			'form' => 'shift_detail'
		);

		return view('employee.shift_detail',compact('col_heads','date','menu','table_info'));
	}

	public function postShift(Request $request){
        $response = ['message' => trans('messages.request_submit'), 'status' => 'success']; 
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	}

	public function shiftDetailList(Request $request){

        $date = ($request->input('date')) ? : date('Y-m-d');
		if(Entrust::can('manage_all_employee'))
			$users = \App\User::all();
		elseif(Entrust::can('manage_subordinate_employee')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
			array_push($child_users, Auth::user()->id);
        	$users = \App\User::whereIn('id',$child_users)->get();
		} else
			$users = \App\User::whereId(Auth::user()->id)->get();

		$rows = array();

        foreach($users as $user){
        	$my_shift = Helper::getShift($date,$user->id);
        	$rows[] = array(
        			$user->full_name_with_designation,
        			$my_shift->OfficeShift->name,
        			($my_shift->in_time != $my_shift->out_time) ? showTime($my_shift->in_time) : '-',
        			($my_shift->in_time != $my_shift->out_time) ? showTime($my_shift->out_time) : '-'
        			);
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function destroy(Clock $clock,Request $request){
        $clock->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.attendance').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.deleted'));
	}
}
?>