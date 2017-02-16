<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\LeaveTypeRequest;
use App\LeaveType;
use App\Classes\Helper;
use Entrust;

Class LeaveTypeController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('leave_type.create');
	}

	public function lists(){
		$leave_types = LeaveType::all();

		$data = '';
		foreach($leave_types as $leave_type){
			$data .= '<tr>
				<td>'.$leave_type->name.'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/leave-type/'.$leave_type->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['leave-type.destroy',$leave_type->id],'leave_type','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(LeaveType $leave_type){
		return view('leave_type.edit',compact('leave_type'));
	}

	public function store(LeaveTypeRequest $request, LeaveType $leave_type){	

		$leave_type->fill($request->all())->save();

		$this->logActivity(['module' => 'leave_type','unique_id' => $leave_type->id,'activity' => 'activity_added']);

        if(\App\Setup::whereModule('leave')->whereCompleted(0)->first())
        	\App\Setup::whereModule('leave')->whereCompleted(0)->update(['completed' => 1]);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $leave_type->name,'id' => $leave_type->id,'field' => 'leave_type_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.leave_type').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
	        if(config('config.application_setup_info') && defaultRole()){
	        	$setup_data = Helper::setupInfo();
	        	$response['setup_data'] = $setup_data;
	        }
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#leave')->withSuccess(trans('messages.leave_type').' '.trans('messages.added'));				
	}

	public function update(LeaveTypeRequest $request, LeaveType $leave_type){

		$leave_type->fill($request->all())->save();

		$this->logActivity(['module' => 'leave_type','unique_id' => $leave_type->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.leave_type').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }
		return redirect('/configuration#leave')->withSuccess(trans('messages.leave_type').' '.trans('messages.updated'));
	}

	public function destroy(LeaveType $leave_type,Request $request){

		$this->logActivity(['module' => 'leave_type','unique_id' => $leave_type->id,'activity' => 'activity_deleted']);

        $leave_type->delete();
        
        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.leave_type').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect('/configuration#leave')->withSuccess(trans('messages.leave_type').' '.trans('messages.deleted'));
	}
}
?>