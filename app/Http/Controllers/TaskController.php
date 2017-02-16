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

        if(defaultRole())
			$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_all_task'))
			$users = \App\User::whereIsHidden(0)->get()->pluck('full_name_with_designation','id')->all();
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

	public function userTask(){

        $col_heads = array(
        		trans('messages.title'),
        		trans('messages.start_date'),
        		trans('messages.date_of_due'),
        		trans('messages.progress'),
        		trans('messages.rating'),
        		trans('messages.rating').' Star',
        		trans('messages.comment')
        		);

        $menu = ['task'];
        $table_info = array(
			'source' => 'user-task',
			'title' => 'User Task',
			'id' => 'user_task_table',
			'form' => 'user_task_form'
		);

        if(defaultRole())
			$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_all_task'))
			$users = \App\User::whereIsHidden(0)->get()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_task')){
			$child_designations = Helper::childDesignation($task->UserAdded->designation_id,1);
			$users = \App\User::whereIn('designation_id',$child_designations)->orWhere('id','=',Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();
		} else 
			$users = \App\User::whereId(Auth::user()->id)->get()->pluck('full_name_with_designation','id')->all();

		return view('task.user_task',compact('col_heads','menu','table_info','users'));
	}

	public function userTaskLists(Request $request){
		$user_id = ($request->input('user_id')) ? : Auth::user()->id;

		$tasks = Task::whereHas('user',function($q) use($user_id){
			$q->where('user_id',$user_id);
		})->get();

		$rows = array();

		foreach($tasks as $task){

			$filtered_task = $task->User->whereLoose('id',$user_id)->first();
			$rating = $filtered_task->pivot->rating;
			$comment = $filtered_task->pivot->comment;

			$rows[] = array(
				$task->title,
				showDate($task->start_date),
				showDate($task->due_date),
				$task->progress.'%',
				(config('config.sub_task_rating')) ? Helper::getSubTaskRating($task->id,$user_id,1) : Helper::getRatingStar($rating,1),
				(config('config.sub_task_rating')) ? Helper::getSubTaskRating($task->id,$user_id) : Helper::getRatingStar($rating),
				$comment
				);
		}
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function userTaskRating(){

        $col_heads = array(
        		trans('messages.employee'),
        		'No of Task',
        		'Completed Task',
        		'Overdue Task',
        		'Average Rating',
        		'Average Rating Star'
        		);

        $menu = ['task'];
        $table_info = array(
			'source' => 'user-task-rating',
			'title' => 'User Task Rating',
			'id' => 'user_task_rating_table',
			'form' => 'user_task_rating_form'
		);

		$locations = \App\Location::all()->pluck('name','id')->all();
		$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
		$designations = \App\Designation::whereIn('id',$child_designations)->get()->pluck('full_designation','id')->all();

		return view('task.user_task_rating',compact('col_heads','menu','table_info','locations','designations'));
	}

	public function userTaskRatingLists(Request $request){

        $rows=array();

        if(defaultRole())
			$users = \App\User::all();
		elseif(Entrust::can('manage_all_task'))
			$users = \App\User::whereIsHidden(0)->get();
		elseif(Entrust::can('manage_subordinate_task')){
			$child_designations = Helper::childDesignation($task->UserAdded->designation_id,1);
			$users = \App\User::whereIn('designation_id',$child_designations)->orWhere('id','=',Auth::user()->id)->get();
		} else 
			$users = \App\User::whereId(Auth::user()->id)->get();

		$location = ($request->has('location_id')) ? \App\Location::whereId($request->input('location_id'))->first() : null;

		if($request->input('designation_id'))
			$users = $users->whereLoose('designation_id',$request->input('designation_id'))->all();

		foreach($users as $user){
			$rating = 0;
			$completed_task = $user->Task->whereLoose('progress','100')->count();
			$overdue_task = $user->Task->filter(function($item){
					return (data_get($item, 'progress') < '100');
				})->filter(function($item) {
					return (data_get($item, 'due_date') < date('Y-m-d'));
				})->count();
			$total_task = $user->Task->count();
			foreach($user->Task as $task){
				if(config('config.sub_task_rating'))
					$rating += Helper::getSubTaskRating($task->id,$user->id,1);
				else
					$rating += $task->pivot->rating;
			}

			$average_rating = ($total_task) ? $rating/$total_task : 0;

			if(!$location || $location->name == Helper::getLocation(date('Y-m-d'),$user->id))
			$rows[] = array(
				$user->full_name_with_designation,
				$total_task,
				$completed_task,
				$overdue_task,
				Helper::getRatingStar($average_rating,1),
				Helper::getRatingStar($average_rating)
			);
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
		$this->logActivity(['module' => 'task','unique_id' => $task->id,'activity' => 'activity_assigned']);

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
		$rating_users = array();
		foreach($task->User as $user){
			$assigned_to[] = $user->id;
			if($task->user_id != $user->id && $user->pivot->rating == null)
				$rating_users[$user->id] = $user->full_name_with_designation;
		}

		if(!in_array(Auth::user()->id,$assigned_to) && $task->user_id != Auth::user()->id)
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		if(defaultRole())
			$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_all_task'))
			$users = \App\User::whereIsHidden(0)->get()->pluck('full_name_with_designation','id')->all();
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

		return view('task.show',compact('task','menu','assets','users','selected_user','rating_users'));
	}

	public function storeRating(Request $request, $id){
		$task = Task::find($id);

		$validation_input['user_id'] = 'required';

		if(!config('config.sub_task_rating'))
			$validation_input['rating'] = 'required';

        $validation = Validator::make($request->all(),$validation_input);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors($validation->messages()->first());
        }

		if(!$task || $task->user_id != Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		if(!config('config.sub_task_rating')){
			$task->user()->sync([$request->input('user_id') => [
				'rating' => $request->input('rating'),
				'comment' => ($request->has('comment')) ? $request->input('comment') : null
			]], false); 
		} else {
			$rating = $request->input('rating');
			$comment = $request->input('comment');
			foreach($task->SubTask as $sub_task){
				$sub_task_rating = \App\SubTaskRating::firstOrNew(['sub_task_id' => $sub_task->id,'user_id' => $request->input('user_id')]);
				$sub_task_rating->sub_task_id = $sub_task->id;
				$sub_task_rating->user_id = $request->input('user_id');
				$sub_task_rating->rating = $rating[$sub_task->id];
				$sub_task_rating->comment = $comment[$sub_task->id];
				$sub_task_rating->save();
			}
		}

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.rating').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task/'.$task->id)->withSuccess(trans('messages.rating').' '.trans('messages.added'));
	}

	public function destroyRating($user_id,$task_id){

		$task = Task::find($task_id);

		if(!$task || $task->user_id != Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		if(config('config.sub_task_rating')){
			$sub_tasks = $task->SubTask->pluck('id')->all();
			\App\SubTaskRating::where('user_id','=',$user_id)->whereIn('sub_task_id',$sub_tasks)->delete();
		} else {
			$valid_rating = DB::table('task_user')->whereTaskId($task->id)->whereUserId($user_id)->whereNotNull('rating')->count();

			if(!$valid_rating){
		        if($request->has('ajax_submit')){
		            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
		            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		        }
				return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
			}

			DB::table('task_user')->whereTaskId($task->id)->whereUserId($user_id)->update([
				'rating' => null,
				'comment' => null
			]);
		}

		return redirect('/task/'.$task_id)->withSuccess(trans('messages.rating').' '.trans('messages.deleted'));
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

		if(defaultRole())
			$users = \App\User::all()->pluck('full_name_with_designation','id')->all();
		elseif(Entrust::can('manage_all_task'))
			$users = \App\User::whereIsHidden(0)->get()->pluck('full_name_with_designation','id')->all();
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

	public function rating($user_id,$task_id){
		$task = Task::find($task_id);
		$user = \App\User::find($user_id);

        $users = $task->User->pluck('id')->all();

		if(!$task || !$user || $task->user_id != Auth::user()->id || !in_array($user->id,$users))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        if(!$task->SubTask->count())
            return view('common.error',['message' => 'Please add atleast one sub task to rate.']);

        return view('task.sub_task_rating',compact('task','user'));
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