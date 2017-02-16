<?php
namespace App\Http\Controllers;
use DB;
use Entrust;
use App\DailyReport;
use App\Classes\Helper;
use Illuminate\Http\Request;
use App\Http\Requests\DailyReportRequest;
use Auth;

Class DailyReportController extends Controller{
    use BasicController;

	protected $form = 'daily-report-form';

	public function index(DailyReport $daily_report){

		if(!Entrust::can('list_daily_report'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.employee'),
        		trans('messages.date')
        		);

        if(defaultRole())
        	$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
        elseif(Entrust::can('manage_all_daily_report'))
        	$users = \App\User::whereIsHidden(0)->get()->pluck('full_name_with_designation','id')->all();
        elseif(Entrust::can('manage_subordinate_daily_report')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = \App\User::whereIn('designation_id',$child_designations)->pluck('id')->all();
        	array_push($child_users,Auth::user()->id);
        	$users = \App\User::whereIn('id',$child_users)->get()->pluck('full_name_with_designation','id')->all();
        }
        else
        	$users = \App\User::whereId(Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();

        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $menu = ['daily_report'];
        $assets = ['rte'];
        $table_info = array(
			'source' => 'daily-report',
			'title' => 'Daily Report List',
			'id' => 'daily_report_table'
		);

		return view('daily_report.index',compact('col_heads','table_info','menu','users','assets'));
	}

	public function lists(){

		if(Entrust::can('manage_all_daily_report'))
			$daily_reports = DailyReport::all();
		elseif(Entrust::can('manage_subordinate_daily_report')) {
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = \App\User::whereIn('designation_id',$child_designations)->pluck('id')->all();
        	array_push($child_users,Auth::user()->id);
			$daily_reports = DailyReport::whereIn('user_id',$child_users)->get();
		} else
			$daily_reports = DailyReport::whereUserId(Auth::user()->id)->get();

        $rows=array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($daily_reports as $daily_report){

			$row = array(
				'<div class="btn-group btn-group-xs">'.
				'<a href="#" data-href="/daily-report/'.$daily_report->id.'" class="btn btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-arrow-circle-right" data-toggle="tooltip" title="'.trans('messages.view').'"></i></a>'.
				((Entrust::can('edit_daily_report') && $this->dailyReportAccessible($daily_report) && !$daily_report->is_locked) ? '<a href="#" data-href="/daily-report/'.$daily_report->id.'/edit" class="btn btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>' : '').
				((Entrust::can('lock_unlock_daily_report') && $this->dailyReportAccessible($daily_report)) ? 
					(($daily_report->is_locked) ? '<a href="#" data-ajax="1" class="btn btn-default" data-source="/daily-report/change-status" data-extra="&id='.$daily_report->id.'"> <i class="fa fa-unlock" data-toggle="tooltip" title="'.trans('messages.unlock').'"></i></a>' : '<a href="#" data-ajax="1" class="btn btn-default" data-source="/daily-report/change-status" data-extra="&id='.$daily_report->id.'" "data-form-table"="daily_report_table"> <i class="fa fa-lock" data-toggle="tooltip" title="'.trans('messages.lock').'"></i></a>')
					: ''). 
				((Entrust::can('delete_daily_report') && $this->dailyReportAccessible($daily_report) && !$daily_report->is_locked) ? delete_form(['daily-report.destroy',$daily_report->id]) : '').
				'</div>',
				$daily_report->User->full_name_with_designation,
				(($daily_report->is_locked) ? '<i class="fa fa-lock"></i>' : '').' '.showDate($daily_report->date)
				);	
			$id = $daily_report->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
        	$rows[] = $row;
			
        }

        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function changeStatus(Request $request){

		$daily_report = DailyReport::find($request->input('id'));

		if(!$daily_report || !$this->dailyReportAccessible($daily_report)) {
			$response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
		    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		}

		if($daily_report->user_id == \Auth::user()->id && !defaultRole()){
			$response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
		    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		}

		$daily_report->is_locked = ($daily_report->is_locked) ? 0 : 1;
		$daily_report->save();

		$this->logActivity(['module' => 'daily_report','unique_id' => $daily_report->id,'activity' => 'activity_status_updated']);

		$response = ['message' => trans('messages.status').' '.trans('messages.updated'), 'status' => 'success']; 
	    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	}

	public function show(DailyReport $daily_report){
		if(!$this->dailyReportAccessible($daily_report))
            return redirect('/daily-report')->withErrors(trans('messages.invalid_link'));

        return view('daily_report.show',compact('daily_report'));
	}

	public function create(){

		if(!Entrust::can('create_daily_report'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        if(defaultRole())
        	$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
        if(Entrust::can('manage_all_daily_report'))
        	$users = \App\User::whereIsHidden(0)->get()->pluck('full_name_with_designation','id')->all();
        elseif(Entrust::can('manage_subordinate_daily_report')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
        	$users = \App\User::whereIn('designation_id',$child_designations)->get()->pluck('full_name_with_designation','id');
        }
        else
        	$users = \App\User::whereId(Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();

        $menu = ['daily_report'];
        $assets = ['rte'];
		return view('daily_report.create',compact('users','menu','assets'));
	}

	public function edit(DailyReport $daily_report){

		if(!Entrust::can('edit_daily_report') || !$this->dailyReportAccessible($daily_report) || $daily_report->is_locked)
            return view('common.error',['message' => trans('messages.permission_denied')]);

        if(defaultRole())
        	$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
        elseif(Entrust::can('manage_all_daily_report'))
        	$users = \App\User::whereIsHidden(0)->get()->pluck('full_name_with_designation','id')->all();
        elseif(Entrust::can('manage_subordinate_daily_report')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
        	$users = \App\User::whereIn('designation_id',$child_designations)->get()->pluck('full_name_with_designation','id');
        }
        else
        	$users = \App\User::whereUserId(Auth::user()->id)->pluck('full_name_with_designation','id')->all();

		$custom_field_values = Helper::getCustomFieldValues($this->form,$daily_report->id);
        $menu = ['daily_report'];
        $assets = ['rte'];

		return view('daily_report.edit',compact('users','daily_report','custom_field_values','menu','assets'));
	}

	public function store(DailyReportRequest $request, DailyReport $daily_report){	

		if(!Entrust::can('create_daily_report')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		$data = $request->all();
	    $daily_report->fill($data);
	    $daily_report->description = clean($request->input('description'));
		$daily_report->save();

		Helper::storeCustomField($this->form,$daily_report->id, $data);
		$this->logActivity(['module' => 'daily_report','unique_id' => $daily_report->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.daily_report').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.daily_report').' '.trans('messages.added'));	
	}

	public function update(DailyReportRequest $request, DailyReport $daily_report){

		if(!Entrust::can('edit_daily_report') || !$this->dailyReportAccessible($daily_report) || $daily_report->is_locked){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }
        
		$data = $request->all();
		$daily_report->fill($data);
	    $daily_report->description = clean($request->input('description'));
		$daily_report->save();

		Helper::updateCustomField($this->form,$daily_report->id, $data);
		$this->logActivity(['module' => 'daily_report','unique_id' => $daily_report->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.daily_report').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/daily-report')->withSuccess(trans('messages.daily_report').' '.trans('messages.updated'));
	}

	public function destroy(DailyReport $daily_report,Request $request){
		if(!Entrust::can('delete_daily_report') || !$this->dailyReportAccessible($daily_report) || $daily_report->is_locked){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		Helper::deleteCustomField($this->form, $daily_report->id);
        
		$this->logActivity(['module' => 'daily_report','unique_id' => $daily_report->id,'activity' => 'activity_deleted']);
        $daily_report->delete();
        
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.daily_report').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/daily-report')->withSuccess(trans('messages.daily_report').' '.trans('messages.deleted'));
	}
}
?>