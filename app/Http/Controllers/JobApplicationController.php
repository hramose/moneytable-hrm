<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\JobApplicationRequest;
use App\Http\Requests\JobApplicationInterviewRequest;
use Entrust;
use Auth;
use App\Classes\Helper;
use App\JobApplication;
use File;
use DB;
use Validator;

Class JobApplicationController extends Controller{
    use BasicController;

	protected $form = 'job-application-form';

	public function __construct()
	{
	    $this->middleware('permission:manage_job_application',['except' => ['apply','store']]);
	}

	public function index(JobApplication $job_application){

		if(Entrust::can('manage_all_job'))
			$jobs = \App\Job::where('date_of_closing','>=',date('Y-m-d'))->get()->pluck('full_job_title','id')->all();
		elseif(Entrust::can('manage_subordinate_job')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$jobs = \App\Job::with('designation')->whereHas('designation',function($q) use($child_designations){
				$q->whereIn('id',$child_designations);
			})->where('date_of_closing','>=',date('Y-m-d'))->get()->pluck('full_job_title','id')->all();
		} else
			$jobs = [];

		$source = Helper::translateList(config('lists.job_application_source'));

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.job'),
        		trans('messages.name'),
        		trans('messages.email'),
        		trans('messages.contact'),
        		trans('messages.source'),
        		trans('messages.status')
        		);

        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $menu = ['job_application'];
        $table_info = array(
			'source' => 'job-application',
			'title' => 'Job Application List',
			'id' => 'job_application_table'
		);

		return view('job_application.index',compact('col_heads','menu','table_info','jobs','source'));
	}

	public function lists(Request $request){

		if(Entrust::can('manage_all_job'))
			$job_applications = JobApplication::all();
		elseif(Entrust::can('manage_subordinate_job')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$job_applications = JobApplication::whereHas('job',function($q) use($child_designations){
				$q->whereIn('designation_id',$child_designations);
			})->get();
		} else
			$job_applications = [];

        $rows=array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($job_applications as $job_application){
			$row = array(
				'<div class="btn-group btn-group-xs">'.
				($this->jobApplicationAccessible($job_application) ? '<a href="/job-application/'.$job_application->id.'" class="btn btn-xs btn-default" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-right"></i></a> ' : '').
				(($job_application->resume != null && $this->jobApplicationAccessible($job_application)) ? 
				'<a href="/job-application/'.$job_application->id.'/resume" class="btn btn-xs btn-default" data-toggle="tooltip" title="'.trans('messages.resume').'"> <i class="fa fa-download"></i></a>' : '').
				($this->jobApplicationAccessible($job_application) ? '<a href="#" data-href="/job-application/'.$job_application->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal" > <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
				($this->jobApplicationAccessible($job_application) ? delete_form(['job-application.destroy',$job_application->id]) : '').
				'</div>',
				$job_application->Job->full_job_title,
				$job_application->name,
				$job_application->email,
				$job_application->contact_number,
				ucwords($job_application->source),
				($job_application->status) ? Helper::toWord($job_application->status) : trans('messages.applied')
				);
			$id = $job_application->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
        	$rows[] = $row;
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function apply(){
		if(!Auth::check() && !config('config.enable_job_application_candidates'))
			return redirect('/dashboard')->withErrors(trans('messages.invalid_link'));

		$job_list = \App\Job::where('date_of_closing','>=',date('Y-m-d'))->get()->pluck('full_job_title','id')->all();
		$jobs = \App\Job::where('date_of_closing','>=',date('Y-m-d'))->get();
		return view('job_application.apply',compact('jobs','job_list'));
	}

	public function show(JobApplication $job_application){

		if(!$this->jobApplicationAccessible($job_application))
			return redirect('/job-application')->withErrors(trans('messages.permission_denied'));

		$job_application_status = Helper::translateList(config('lists.job_application_status'));

		$assets = ['datetimepicker'];
        $menu = ['job_application'];
		return view('job_application.show',compact('job_application','job_application_status','assets','menu'));
	}

	public function create(){
		if(Entrust::can('manage_all_job'))
			$jobs = \App\Job::where('date_of_closing','>=',date('Y-m-d'))->get()->pluck('full_job_title','id')->all();
		elseif(Entrust::can('manage_subordinate_job')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$jobs = \App\Job::with('designation')->whereHas('designation',function($q) use($child_designations){
				$q->whereIn('id',$child_designations);
			})->where('date_of_closing','>=',date('Y-m-d'))->get()->pluck('full_job_title','id')->all();
		} else
			$jobs = [];

		$source = Helper::translateList(config('lists.job_application_source'));
        $menu = ['job_application'];
		return view('job_application.create',compact('jobs','source','menu'));
	}

	public function edit(JobApplication $job_application){
		if(!$this->jobApplicationAccessible($job_application))
			return redirect('/job-application')->withErrors(trans('messages.permission_denied'));

		if(Entrust::can('manage_all_job'))
			$jobs = \App\Job::where('date_of_closing','>=',date('Y-m-d'))->get()->pluck('full_job_title','id')->all();
		elseif(Entrust::can('manage_subordinate_job')){
			$child_designations = Helper::childDesignation($job_application->designation_id,1);
			$jobs = \App\Job::with('designation')->whereHas('designation',function($q) use($child_designations){
				$q->whereIn('id',$child_designations);
			})->where('date_of_closing','>=',date('Y-m-d'))->get()->pluck('full_job_title','id')->all();
		} else
			$jobs = [];
		$source = Helper::translateList(config('lists.job_application_source'));
		$custom_field_values = Helper::getCustomFieldValues($this->form,$job_application->id);
        $menu = ['job_application'];
		return view('job_application.edit',compact('jobs','custom_field_values','job_application','source','menu'));
	}

	public function store(JobApplicationRequest $request, JobApplication $job_application){	
        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		$data = $request->all();
		$job = \App\Job::find($request->input('job_id'));

		if($request->input('date_of_application') > $job->date_of_closing){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.application_date_must_be_less_than_closing_date'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.application_date_must_be_less_than_closing_date'));
		}

		if($request->has('date_of_application') && $request->input('date_of_application') < $job->created_at){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.application_date_must_be_greater_tha_job_posting_date'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.application_date_must_be_greater_tha_job_posting_date'));
		}

		if ($request->hasFile('resume')) {
	        $filename = $request->file('resume')->getClientOriginalName();
	        $extension = $request->file('resume')->getClientOriginalExtension();
	        $filename = uniqid();
	        $file = $request->file('resume')->move(config('constants.upload_path.attachments'), $filename.".".$extension);
        	$data['resume'] = $filename.".".$extension;
	    } else 
	    $data['resume'] = null;

	    $data['date_of_application'] = ($request->input('date_of_application')) ? : date('Y-m-d');
	    $data['source'] = ($request->has('source')) ? $request->input('source') : 'website';
        $data['status'] = 'applied';
        $job_application->fill($data);
        $job_application->user_id = (Auth::check()) ? Auth::user()->id : null;
        $job_application->save();
		Helper::storeCustomField($this->form,$job_application->id, $data);
		$this->logActivity(['module' => 'job_application','unique_id' => $job_application->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
            $response = ['message' => ($data['source'] != 'website') ? (trans('messages.job_application').' '.trans('messages.added')) : trans('messages.application_submit'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        if($data['source'] != 'website')
       		return redirect()->back()->withSuccess(trans('messages.job_application').' '.trans('messages.added'));
       	else
       		return redirect()->back()->withSuccess(trans('messages.application_submit'));
    }

	public function update(JobApplicationRequest $request, JobApplication $job_application){
		if(!$this->jobApplicationAccessible($job_application))
			return redirect('/job-application')->withErrors(trans('messages.permission_denied'));
		
        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }
		
		$data = $request->all();
		$job = $job_application->Job;

		if($request->input('date_of_application') > $job_application->Job->date_of_closing)
			return redirect()->back()->withErrors(trans('messages.application_date_must_be_less_than_closing_date'));

		if($request->has('date_of_application') && $request->input('date_of_application') < $job->created_at)
			return redirect()->back()->withErrors(trans('messages.application_date_must_be_greater_tha_job_posting_date'));

		if($request->hasFile('resume')){
			if(File::exists(config('constants.upload_path.attachments').$job_application->resume))
			File::delete(config('constants.upload_path.attachments').$job_application->resume);
	        $filename = $request->file('resume')->getClientOriginalName();
	        $extension = $request->file('resume')->getClientOriginalExtension();
	        $filename = uniqid();
	        $file = $request->file('resume')->move(config('constants.upload_path.attachments'), $filename.".".$extension);
	        $data['resume'] = $filename.".".$extension;
		} else
		$data['resume'] = $job_application->resume;
	    $data['source'] = ($request->has('source')) ? $request->input('source') : $job_application->source;
        $job_application->fill($data)->save();
		Helper::updateCustomField($this->form,$job_application->id, $data);
		$this->logActivity(['module' => 'job_application','unique_id' => $job_application->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.job_application').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/job-application')->withSuccess(trans('messages.job_application').' '.trans('messages.updated'));
	}

	public function resume($id){
		$job_application = JobApplication::find($id);

		if(!$job_application)
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		if(!$this->jobApplicationAccessible($job_application))
			return redirect('/job-application')->withErrors(trans('messages.permission_denied'));

		$file = config('constants.upload_path.attachments').$job_application->resume;

		if(File::exists($file))
			return response()->download($file);
		else
			return redirect()->back()->withErrors(trans('messages.file_not_found'));

	}

	public function updateStatus(Request $request,$id){

        $validation = Validator::make($request->all(),[
            'status' => 'required'
        ]);

        if($validation->fails()){
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		$job_application = JobApplication::find($id);

		if(!$job_application)
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$job_application->status = $request->input('status');
		$job_application->remarks = $request->input('remarks');

		$job_application->save();
		$this->logActivity(['module' => 'job_application','unique_id' => $job_application->id,'activity' => 'activity_status_updated']);

		return redirect()->back()->withSuccess(trans('messages.job_application').' '.trans('messages.status').' '.trans('messages.updated'));
	}
	
	public function destroy(JobApplication $job_application,Request $request){

		if(!$this->jobApplicationAccessible($job_application))
			return redirect('/job-application')->withErrors(trans('messages.permission_denied'));

		Helper::deleteCustomField($this->form, $job_application->id);
        
		if(File::exists(config('constants.upload_path.attachments').$job_application->resume))
		File::delete(config('constants.upload_path.attachments').$job_application->resume);
		$this->logActivity(['module' => 'job_application','unique_id' => $job_application->id,'activity' => 'activity_deleted']);
        $job_application->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.job_application').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/job-application')->withSuccess(trans('messages.job_application').' '.trans('messages.deleted'));
	}
}
?>