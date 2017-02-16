<?php
namespace App\Http\Controllers;
use App\Classes\Helper;
use App\TicketAttachment;
use File;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\TicketAttachmentRequest;

Class TicketAttachmentController extends Controller{
    use BasicController;

	public function store(TicketAttachmentRequest $request,$id){

		$filename = uniqid();

		$ticket_attachment = new TicketAttachment;
     	if ($request->hasFile('attachments')) {
	 		$extension = $request->file('attachments')->getClientOriginalExtension();
	 		$file = $request->file('attachments')->move(config('constants.upload_path.attachments'), $filename.".".$extension);
	 		$ticket_attachment->attachments = $filename.".".$extension;
		 }

		$ticket_attachment->title = $request->input('title');
		$ticket_attachment->description = $request->input('description');
		$ticket_attachment->user_id = Auth::user()->id;
		$ticket_attachment->ticket_id = $id;
		$this->logActivity(['module' => 'ticket_attachment','unique_id' => $ticket_attachment->id,'secondary_id' => $id, 'activity' => 'activity_added']);

		$ticket_attachment->save();
        if($request->has('ajax_submit')){
        	$data = $this->lists($id);
            $response = ['message' => trans('messages.attachment').' '.trans('messages.added'), 'status' => 'success','data' => $data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/ticket/'.$id."#attachment")->withSuccess(trans('messages.attachment').' '.trans('messages.added'));
	}

	public function lists($id){
        $data = '';

        $ticket = \App\Ticket::find($id);

        if(!$ticket)
            return $data;

		foreach($ticket->TicketAttachment as $ticket_attachment){
			$data .= '<tr>
				<td><div class="btn-group btn-group-xs">';
			if(Auth::user()->id == $ticket_attachment->user_id)
			$data .= delete_form(['ticket-attachment.destroy',$ticket_attachment->id]);
			$data .= '<a href="/ticket-attachment/download/'.$ticket_attachment->id.'" class="btn btn-xs btn-default" ><i class="fa fa-download"></i></a></div></td>
				<td>'.$ticket_attachment->title.'</td>
				<td>'.$ticket_attachment->description.'</td>
				<td>'.showDateTime($ticket_attachment->created_at).'</td>
			</tr>';
		}

		return $data;
	}

	public function download($id){
		$ticket_attachment = TicketAttachment::find($id);

		$file = config('constants.upload_path.attachments').$ticket_attachment->attachments;

		if(File::exists($file))
			return response()->download($file);
		else
			return redirect()->back()->withErrors(trans('messages.file_not_found'));
	}

	public function destroy($id,Request $request){
		$ticket_attachment = TicketAttachment::find($id);

		if($ticket_attachment->user_id != Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		$this->logActivity(['module' => 'ticket_attachment','unique_id' => $ticket_attachment->id,'secondary_id' => $ticket_attachment->Ticket->id, 'activity' => 'activity_deleted']);
		$id = $ticket_attachment->Ticket->id;
		File::delete(config('constants.upload_path.attachments').$ticket_attachment->attachments);
		$ticket_attachment->delete($id);
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.attachment').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/ticket/'.$id.'#attachment')->withSuccess(trans('messages.attachment').' '.trans('messages.deleted'));
	}
}
?>