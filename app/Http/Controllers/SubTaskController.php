<?php
namespace App\Http\Controllers;
use App\Classes\Helper;
use App\SubTask;
use Auth;
use Illuminate\Http\Request;
use Validator;

Class SubTaskController extends Controller{
    use BasicController;

    public function store(Request $request){
        $validation = Validator::make($request->all(),[
            'title' => 'required|unique_with:sub_tasks,task_id',
        ]);

        if($validation->fails()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        $sub_task = new SubTask;
        $sub_task->task_id = $request->input('task_id');
        $sub_task->title = $request->input('title');
        $sub_task->description = $request->input('description');
        $sub_task->user_id = Auth::user()->id;
        $sub_task->save();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.sub_task').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withInput()->withSuccess(trans('messages.sub_task').' '.trans('messages.added'));
    }

    public function lists(Request $request){
        $data = '';

        $task = \App\Task::find($request->input('task_id'));

        if(!$task)
            return $data;

        foreach($task->SubTask as $sub_task){
        $data .= '<tr>
                <td>'.$sub_task->title.'</td>
                <td>'.$sub_task->description.'</td>
                <td>'.showDate($sub_task->created_at).'</td>
                <td>
                    <div class="btn-group btn-group-xs">
                        <a href="#" data-href="/sub-task/'.$sub_task->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
                        delete_form(['sub-task.destroy',$sub_task->id]).
                    '</div>
                </td>
            </tr>';
        }

        return $data;
    }

    public function showRating($user_id,$task_id){
        $task = \App\Task::find($task_id);
        $user = \App\User::find($user_id);

    	if(!config('config.sub_task_rating') || !$task || !$user)
            return view('common.error',['message' => trans('messages.permission_denied')]);

        return view('task.view_sub_task_rating',compact('task','user'));

    }

    public function edit($id){
    	$sub_task = SubTask::find($id);
    	if(!$sub_task || $sub_task->user_id != Auth::user()->id)
            return view('common.error',['message' => trans('messages.permission_denied')]);

        return view('task.edit_sub_task',compact('sub_task'));
    }

    public function update($id,Request $request){
    	$sub_task = SubTask::find($id);

    	if(!$sub_task || $sub_task->user_id != Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.permission_denied'));
		}

        $validation = Validator::make($request->all(),[
            'title' => 'required'
        ]);

        if($validation->fails()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        $count_sub_task = SubTask::where('id','!=',$id)->whereTaskId($sub_task->id)->whereTitle($request->input('title'))->count();

        if($count_sub_task){
	        if($request->has('ajax_submit')){
	            $response = ['message' => 'This title has already been taken.', 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
            return redirect()->back()->withInput()->withErrors('This title has already been taken.');
        }

        $sub_task->title = $request->input('title');
        $sub_task->description = $request->input('description');
        $sub_task->save();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.sub_task').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.sub_task').' '.trans('messages.updated'));
    }

    public function destroy($id,Request $request){
    	$sub_task = SubTask::find($id);

    	if(!$sub_task || $sub_task->user_id != Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.permission_denied'));
		}

		$this->logActivity(['module' => 'sub_task','unique_id' => $sub_task->id,'activity' => 'activity_deleted']);
		$sub_task->delete();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.sub_task').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.sub_task').' '.trans('messages.deleted'));
    }
}