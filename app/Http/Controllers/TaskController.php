<?php
namespace App\Http\Controllers;
use DB;
use Entrust;
use App\Classes\Helper;
use App\Task;
use App\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use Validator;

Class TaskController extends Controller{
    use BasicController;

	protected $form = 'task-form';

	public function index(Task $task){

		if(!Entrust::can('list_task'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.title'),
        		trans('messages.created_by'),
        		trans('messages.assigned_to'),
        		trans('messages.start_date'),
        		trans('messages.date_of_due'),
        		trans('messages.progress')
        		);

        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $menu = ['task'];
        $assets = ['rte'];
        $table_info = array(
			'source' => 'task',
			'title' => 'Task List',
			'id' => 'task_table'
		);

		if(Entrust::can('manage_all_task'))
			$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_task')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$users = \App\User::whereIn('designation_id',$child_designations)->orWhere('id','=',Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();
		} else 
			$users = \App\User::whereId(Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();

		return view('task.index',compact('col_heads','menu','table_info','users','assets'));
	}

	public function lists(Request $request){

		if(Entrust::can('manage_all_task'))
			$tasks = Task::all();
		elseif(Entrust::can('manage_subordinate_task')) {

			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
			array_push($child_users, Auth::user()->id);

			$tasks = Task::whereHas('user', function($q) use($child_users){
			    $q->whereIn('user_id',$child_users);
			})->get();
		} else 
			$tasks = Task::whereHas('user', function($q){
			    $q->where('user_id','=',Auth::user()->id);
			})->get();

        $rows=array();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);

        foreach($tasks as $task){
        	$task_user = "<ol>";
        	foreach($task->User as $user)
			    $task_user .= "<li>$user->full_name_with_designation</li>";
        	$task_user .= "</ol>";

			$row = array(
				'<div class="btn-group btn-group-xs">'.'<a href="/task/'.$task->id.'" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-right"></i></a>'.
				((Entrust::can('edit_task') && $this->taskAccessible($task)) ? '<a href="#" data-href="/task/'.$task->id.'/edit" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>' : '').
				((Entrust::can('delete_task') && $this->taskAccessible($task)) ? delete_form(['task.destroy',$task->id]) : '').
				'</div>', 
				$task->title,
				$task->userAdded->full_name_with_designation,
				$task_user,
				showDate($task->start_date),
				showDate($task->due_date),
				$task->progress.' %'
				);	
			$id = $task->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');

        	$rows[] = $row;
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function assignTask(Request $request,$id){

		if(!Entrust::can('assign_task')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$task = Task::find($id);

		if(!$task){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/task')->withErrors(trans('messages.invalid_link'));
		}

	    $task->user()->sync(($request->input('user_id')) ? : []);
		$this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_user_assigned']);

        if($request->has('ajax_submit')){
			$new_data = '';
			foreach($task->User as $user){
				$new_data .= '<li class="media">'.Helper::getAvatar($user->id).
				'<div class="media-body" style="vertical-align:middle; padding-left:10px;">
				  <h4 class="media-heading"><a href="#">'.$user->full_name.'</a> <br /> <small>'.$user->Designation->full_designation.'</small></h4>';
				  if($user->id == $task->user_id)
					$new_data .= '<span class="label label-danger pull-right">Admin</span>';
				$new_data .= '</div>
			  </li>';
			}
			$new_data .= '<script>$("#task-assigned-user .textAvatar").nameBadge();</script>';
            $response = ['message' => trans('messages.task').' '.trans('messages.user').' '.trans('messages.assigned'), 'status' => 'success','new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.task').' '.trans('messages.user').' '.trans('messages.assigned'));	
	}

	public function updateTaskProgress(Request $request,$id){

		if(!Entrust::can('update_task_progress')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $validation = Validator::make($request->all(),[
            'progress' => 'required|numeric|min:0|max:100'
        ]);

        if($validation->fails()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

		$task = Task::find($id);

		if(!$task){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/task')->withErrors(trans('messages.invalid_link'));
		}

		$task->progress =$request->input('progress');
		$task->save();
		$this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_status_updated']);

        if($request->has('ajax_submit')){
        	$color = Helper::activityTaskProgressColor($task->progress);
        	$new_data = '<div class="progress-bar progress-bar-'.$color.'" role="progressbar" aria-valuenow="'.$task->progress.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$task->progress.'%;">'.$task->progress.'%
			  	</div>';
            $response = ['message' => trans('messages.task').' '.trans('messages.status').' '.trans('messages.updated'), 'status' => 'success','new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.task').' '.trans('messages.status').' '.trans('messages.updated'));
	}

	public function show(Task $task){

		$assigned_to = array();
		foreach($task->User as $user)
			$assigned_to[] = $user->id;

		if(!in_array(Auth::user()->id,$assigned_to) && $task->user_id != Auth::user()->id)
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		if(Entrust::can('manage_all_task'))
			$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_task')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$users = \App\User::whereIn('designation_id',$child_designations)->orWhere('id','=',Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();
		} else 
			$users = \App\User::whereId(Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();
		
		$selected_user = array();
		foreach($task->User as $user)
			$selected_user[] = $user->id;

        $menu = ['task'];
        $assets = ['rte'];

		return view('task.show',compact('task','menu','assets','users','selected_user'));
	}

	public function create(){

		if(!Entrust::can('create_task'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		if(Entrust::can('manage_all_task'))
			$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_task')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$users = \App\User::whereIn('designation_id',$child_designations)->orWhere('id','=',Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();
		} else 
			$users = \App\User::whereId(Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();
        $menu = ['task'];
		
		return view('task.create',compact('users','menu'));
	}

	public function edit(Task $task){

		if(!Entrust::can('edit_task') || !$this->taskAccessible($task))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$selected_user = array();

		foreach($task->User as $user)
			$selected_user[] = $user->id;

		if(Entrust::can('manage_all_task'))
			$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_task')){
			$child_designations = Helper::childDesignation($task->UserAdded->designation_id,1);
			$users = \App\User::whereIn('designation_id',$child_designations)->orWhere('id','=',Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();
		} else 
			$users = \App\User::whereId(Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();

		$custom_field_values = Helper::getCustomFieldValues($this->form,$task->id);
        $menu = ['task'];
        $assets = ['rte'];
		return view('task.edit',compact('users','task','selected_user','custom_field_values','menu','assets'));
	}

	public function store(TaskRequest $request, Task $task){

		if(!Entrust::can('create_task')){
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
	    $task->fill($data);
	    $task->description = clean($request->input('description'));
	    $task->user_id = Auth::user()->id;
		$task->save();
	    $task->user()->sync(($request->input('user_id')) ? : []);
		Helper::storeCustomField($this->form,$task->id, $data);
		$this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.task').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.task').' '.trans('messages.added'));	
	}

	public function update(TaskRequest $request, Task $task){

		if(!Entrust::can('edit_task') || !$this->taskAccessible($task)){
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
		$task->fill($data);
	    $task->description = clean($request->input('description'));
		$task->save();
	    $task->user()->sync(($request->input('user_id')) ? : []);
		Helper::updateCustomField($this->form,$task->id, $data);
		$this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_updated']);
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.task').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task')->withSuccess(trans('messages.task').' '.trans('messages.updated'));
	}

	public function destroy(Task $task,Request $request){
		if(!Entrust::can('delete_task') || !$this->taskAccessible($task)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_deleted']);
		Helper::deleteCustomField($this->form, $task->id);
		$task->delete();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.task').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/task')->withSuccess(trans('messages.task').' '.trans('messages.deleted'));
	}
}
?>