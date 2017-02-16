<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\JobRequest;
use Entrust;
use Auth;
use App\Classes\Helper;
use App\Job;
use Config;
use File;
use DB;

Class JobController extends Controller{
    use BasicController;

	protected $form = 'job-form';

	public function index(Job $job){

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.job').' '.trans('messages.title'),
        		trans('messages.designation'),
        		trans('messages.job_type'),
        		trans('messages.date_of_closing'),
        		trans('messages.location'),
        		trans('messages.no_of_post'),
        		trans('messages.published_by')
        		);

		if(Entrust::can('manage_all_job'))
			$designations = \App\Designation::all()->pluck('full_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_job')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$designations = \App\Designation::whereIn('id',$child_designations)->get()->pluck('full_designation','id')->all();
		} else
			$designations = [];

        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $menu = ['job','list_job'];
        $assets = ['rte'];
        $table_info = array(
			'source' => 'job',
			'title' => 'Job List',
			'id' => 'job_table'
		);
		$job_types = Helper::translateList(config('lists.job_type'));

		return view('job.index',compact('col_heads','menu','table_info','job_types','designations','assets'));
	}

	public function lists(Request $request){

		if(Entrust::can('manage_all_job'))
			$jobs = Job::all();
		elseif(Entrust::can('manage_subordinate_job')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$jobs = Job::whereIn('designation_id',$child_designations)->get();
		} else
			$jobs = [];

        $rows=array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($jobs as $job){
			$row = array(
				'<div class="btn-group btn-group-xs">'.
				((Entrust::can('edit_job') && $this->jobAccessible($job)) ? '<a href="#" data-href="/job/'.$job->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
				((Entrust::can('delete_job') && $this->jobAccessible($job)) ? delete_form(['job.destroy',$job->id]) : '').
				'</div>',
				$job->title,
				$job->Designation->full_designation,
				trans('messages.'.$job->job_type),
				showDate($job->date_of_closing),
				$job->location,
				$job->no_of_post,
				$job->User->full_name_with_designation
				);
			$id = $job->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
        	$rows[] = $row;
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function show(Job $job){
	}

	public function create(){

		if(!Entrust::can('create_job'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		if(Entrust::can('manage_all_job'))
			$designations = \App\Designation::all()->pluck('full_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_job')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$designations = \App\Designation::whereIn('id',$child_designations)->get()->pluck('full_designation','id')->all();
		} else
			$designations = [];

		$job_types = Helper::translateList(config('lists.job_type'));
        $menu = ['job','list_job'];
		return view('job.create',compact('designations','job_types','menu'));
	}

	public function edit(Job $job){

		if(!Entrust::can('edit_job') || !$this->jobAccessible($job))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		if(Entrust::can('manage_all_job'))
			$designations = \App\Designation::all()->pluck('full_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_job')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$designations = \App\Designation::whereIn('id',$child_designations)->get()->pluck('full_designation','id')->all();
		} else
			$designations = [];

        $assets = ['rte'];
		$job_types = Helper::translateList(config('lists.job_type'));
		$custom_field_values = Helper::getCustomFieldValues($this->form,$job->id);
        $menu = ['job','list_job'];
		return view('job.edit',compact('job','custom_field_values','designations','job_types','menu','assets'));
	}

	public function store(JobRequest $request, Job $job){	
		if(!Entrust::can('create_job')){
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
		$data['user_id'] = Auth::user()->id;
		$job->fill($data);
		$job->description = clean($request->input('description'));
		$job->save();

		Helper::storeCustomField($this->form,$job->id, $data);

		$this->logActivity(['module' => 'job','unique_id' => $job->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.job_opening').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

		return redirect()->back()->withSuccess(trans('messages.job_opening').' '.trans('messages.added'));		
	}

	public function update(JobRequest $request, Job $job){
		if(!Entrust::can('edit_job') || !$this->jobAccessible($job)){
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
		$data['user_id'] = $job->user_id;
		$job->fill($data);
		$job->description = clean($request->input('description'));
		$job->save();

		Helper::updateCustomField($this->form,$job->id, $data);
		$this->logActivity(['module' => 'job','unique_id' => $job->id,'activity' => 'activity_updated']);
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.job_opening').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/job')->withSuccess(trans('messages.job_opening').' '.trans('messages.updated'));
	}
	
	public function destroy(Job $job,Request $request){
		if(!Entrust::can('delete_job') || !$this->jobAccessible($job)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		Helper::deleteCustomField($this->form, $job->id);
        
		$this->logActivity(['module' => 'job','unique_id' => $job->id,'activity' => 'activity_deleted']);
        $job->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.job_opening').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/job')->withSuccess(trans('messages.job_opening').' '.trans('messages.deleted'));
	}
}
?>