<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\MessagePriorityRequest;
use App\MessagePriority;
use App\Classes\Helper;
use Entrust;

Class MessagePriorityController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('message_priority.create');
	}

	public function lists(){
		$message_priorities = MessagePriority::all();

		$data = '';
		foreach($message_priorities as $message_priority){
			$data .= '<tr>
				<td>'.$message_priority->name.'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/message-priority/'.$message_priority->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['message-priority.destroy',$message_priority->id],'message_priority','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(MessagePriority $message_priority){
		return view('message_priority.edit',compact('message_priority'));
	}

	public function store(MessagePriorityRequest $request, MessagePriority $message_priority){	

		$message_priority->fill($request->all())->save();

		$this->logActivity(['module' => 'message_priority','unique_id' => $message_priority->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $message_priority->name,'id' => $message_priority->id,'field' => 'message_priority_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.message_priority').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#leave')->withSuccess(trans('messages.message_priority').' '.trans('messages.added'));				
	}

	public function update(MessagePriorityRequest $request, MessagePriority $message_priority){

		$message_priority->fill($request->all())->save();

		$this->logActivity(['module' => 'message_priority','unique_id' => $message_priority->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.message_priority').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }
		return redirect('/configuration#leave')->withSuccess(trans('messages.message_priority').' '.trans('messages.updated'));
	}

	public function destroy(MessagePriority $message_priority,Request $request){

		$this->logActivity(['module' => 'message_priority','unique_id' => $message_priority->id,'activity' => 'activity_deleted']);

        $message_priority->delete();
        
        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.message_priority').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect('/configuration#leave')->withSuccess(trans('messages.message_priority').' '.trans('messages.deleted'));
	}
}
?>