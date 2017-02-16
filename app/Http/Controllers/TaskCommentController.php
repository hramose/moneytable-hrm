<?php
namespace App\Http\Controllers;
use App\TaskComment;
use App\Classes\Helper;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\TaskCommentRequest;

Class TaskCommentController extends Controller{
    use BasicController;

	public function store(TaskCommentRequest $request, $id){

		$task_comment = new TaskComment;
	    $task_comment->fill($request->all());
	    $task_comment->comment = clean($request->input('comment'));
	    $task_comment->task_id = $id;
	    $task_comment->user_id = Auth::user()->id;
	    $task_comment->save();
		$this->logActivity(['module' => 'task_comment','unique_id' => $task_comment->id,'secondary_id' => $id, 'activity' => 'activity_added']);
	    
        if($request->has('ajax_submit')){
        	$new_data = '<li class="media" id="comment_'.$task_comment->id.'">
					<a class="pull-left" href="#">'.Helper::getAvatar($task_comment->user_id).
					'</a>
					<div class="media-body">
					  <h4 class="media-heading"><a href="#">'.$task_comment->User->full_name.'</a> <small>'.showDateTime($task_comment->created_at).'</small>';
					  if(Auth::user()->id == $task_comment->user_id)
					$new_data .= '<div class="pull-right">'.delete_form(['task-comment.destroy',$task_comment->id]).'</div>';
					$new_data .= '</h4>
					  <div class="the-notes danger" style="margin-top:10px; background-color:#f1f1f1;">'.$task_comment->comment.'</div>
					</div>
				  </li><script>$("#comment_'.$task_comment->id.' .textAvatar").nameBadge();</script>';
				  
            $response = ['message' => trans('messages.comment').' '.trans('messages.posted'), 'status' => 'success','new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
	    return redirect('/task/'.$id."#comment")->withSuccess(trans('messages.comment').' '.trans('messages.posted'));
	}

	public function destroy($id,Request $request){

		$task_comment = TaskComment::find($id);

		if($task_comment->user_id != Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		$this->logActivity(['module' => 'task_comment','unique_id' => $task_comment->id,'secondary_id' => $task_comment->Task->id, 'activity' => 'activity_deleted']);
		$id = $task_comment->Task->id;
		$task_comment->delete();
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.comment').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task/'.$id.'#comment')->withSuccess(trans('messages.comment').' '.trans('messages.deleted'));
	}
}
?>