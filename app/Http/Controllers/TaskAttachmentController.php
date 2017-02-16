<?php
namespace App\Http\Controllers;
use App\Classes\Helper;
use App\TaskAttachment;
use File;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\TaskAttachmentRequest;

Class TaskAttachmentController extends Controller{
    use BasicController;

	public function store(TaskAttachmentRequest $request,$id){

		if($request->hasFile('attachments') && Helper::getUsedStorage() > config('limit.storage')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.not_enough_storage'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.not_enough_storage'));
		}
		
		$filename = uniqid();

		$task_attachment = new TaskAttachment;
     	if ($request->hasFile('attachments')) {
	 		$extension = $request->file('attachments')->getClientOriginalExtension();
	 		$file = $request->file('attachments')->move(config('constants.upload_path.attachments'), $filename.".".$extension);
	 		$task_attachment->attachments = $filename.".".$extension;
		 }

		$task_attachment->title = $request->input('title');
		$task_attachment->description = $request->input('description');
		$task_attachment->user_id = Auth::user()->id;
		$task_attachment->task_id = $id;
		$this->logActivity(['module' => 'task_attachment','unique_id' => $task_attachment->id,'secondary_id' => $id, 'activity' => 'activity_added']);

		$task_attachment->save();

        if($request->has('ajax_submit')){
        	$data = $this->lists($id);
            $response = ['message' => trans('messages.attachment').' '.trans('messages.added'), 'status' => 'success','data' => $data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task/'.$id."#attachment")->withSuccess(trans('messages.attachment').' '.trans('messages.added'));
	}

	public function lists($id){
        $data = '';

        $task = \App\Task::find($id);

        if(!$task)
            return $data;

		foreach($task->TaskAttachment as $task_attachment){
			$data .= '<tr>
				<td><div class="btn-group btn-group-xs">';
			if(Auth::user()->id == $task_attachment->user_id)
			$data .= delete_form(['task-attachment.destroy',$task_attachment->id]);
			$data .= '<a href="/task-attachment/download/'.$task_attachment->id.'" class="btn btn-xs btn-default" ><i class="fa fa-download"></i></a></div></td>
				<td>'.$task_attachment->title.'</td>
				<td>'.$task_attachment->description.'</td>
				<td>'.showDateTime($task_attachment->created_at).'</td>
			</tr>';
		}

		return $data;
	}

	public function download($id){
		$task_attachment = TaskAttachment::find($id);

		$file = config('constants.upload_path.attachments').$task_attachment->attachments;

		if(File::exists($file))
			return response()->download($file);
		else
			return redirect()->back()->withErrors(trans('messages.file_not_found'));
	}

	public function destroy($id,Request $request){

		$task_attachment = TaskAttachment::find($id);

		if($task_attachment->user_id != Auth::user()->id){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.invalid_link'));
		}

		$this->logActivity(['module' => 'task_attachment','unique_id' => $task_attachment->id,'secondary_id' => $task_attachment->Task->id, 'activity' => 'activity_deleted']);
		$id = $task_attachment->Task->id;
		File::delete(config('constants.upload_path.attachments').$task_attachment->attachments);
		$task_attachment->delete($id);
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.attachment').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/task/'.$id.'#attachment')->withSuccess(trans('messages.attachment').' '.trans('messages.deleted'));
	}
}
?>