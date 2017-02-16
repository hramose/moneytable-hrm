<?php
namespace App\Http\Controllers;
use App\TicketComment;
use App\Classes\Helper;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\TicketCommentRequest;

Class TicketCommentController extends Controller{
    use BasicController;

	public function store(TicketCommentRequest $request, $id){

		$ticket_comment = new TicketComment;
	    $ticket_comment->fill($request->all());
	    $ticket_comment->comment = clean($request->input('comment'));
	    $ticket_comment->ticket_id = $id;
	    $ticket_comment->user_id = Auth::user()->id;
	    $ticket_comment->save();
		$this->logActivity(['module' => 'ticket_comment','unique_id' => $ticket_comment->id,'secondary_id' => $id, 'activity' => 'activity_added']);
	    
        if($request->has('ajax_submit')){
        	$new_data = '<li class="media" id="comment_'.$ticket_comment->id.'">
					<a class="pull-left" href="#">'.Helper::getAvatar($ticket_comment->user_id).
					'</a>
					<div class="media-body">
					  <h4 class="media-heading"><a href="#">'.$ticket_comment->User->full_name.'</a> <small>'.showDateTime($ticket_comment->created_at).'</small>';
					  if(Auth::user()->id == $ticket_comment->user_id)
					$new_data .= '<div class="pull-right">'.delete_form(['task-comment.destroy',$ticket_comment->id]).'</div>';
					$new_data .= '</h4>
					  <div class="the-notes danger" style="margin-top:10px; background-color:#f1f1f1;">'.$ticket_comment->comment.'</div>
					</div>
				  </li><script>$("#comment_'.$ticket_comment->id.' .textAvatar").nameBadge();</script>';
				  
            $response = ['message' => trans('messages.comment').' '.trans('messages.posted'), 'status' => 'success','new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
	    return redirect('/ticket/'.$id."#comment")->withSuccess(trans('messages.comment').' '.trans('messages.added'));
	}

	public function destroy($id,Request $request){

		$ticket_comment = TicketComment::find($id);

		if($ticket_comment->user_id != Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		$this->logActivity(['module' => 'ticket_comment','unique_id' => $ticket_comment->id,'secondary_id' => $ticket_comment->Ticket->id, 'activity' => 'activity_deleted']);
		$id = $ticket_comment->Ticket->id;
		$ticket_comment->delete();
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.comment').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/ticket/'.$id.'#comment')->withSuccess(trans('messages.comment').' '.trans('messages.deleted'));
	}
}
?>