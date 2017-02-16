<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Role;
use Entrust;
use App\Classes\Helper;

Class RoleController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('role.create');
	}

	public function lists(){
		$roles = Role::all();

		$data = '';
		foreach($roles as $role){
			$data .= '<tr>
				<td>'.Helper::toWord($role->name).' '.(($role->is_hidden) ? '<span class="label label-danger">'.trans('messages.default').'</span>' : '').(($role->is_default) ? '<span class="label label-warning">'.trans('messages.user').' '.trans('messages.default').'</span>' : '').'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/role/'.$role->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['role.destroy',$role->id],'role','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(Role $role){
		return view('role.edit',compact('role'));
	}

	public function store(RoleRequest $request,Role $role){	

		$role->fill($request->all())->save();

		$this->logActivity(['module' => 'role','unique_id' => $role->id,'activity' => 'activity_added']);

		if(\App\Setup::whereModule('role')->whereCompleted(0)->first())
        	\App\Setup::whereModule('role')->whereCompleted(0)->update(['completed' => 1]);

        if($request->has('is_default')){
        	\App\Role::whereNotNull('id')->update(['is_default' => 0]);
        	$role->is_default = 1;
        	$role->save();
        }

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $role->name,'id' => $role->id,'field' => 'role_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.role').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
	        if(config('config.application_setup_info') && defaultRole()){
	        	$setup_data = Helper::setupInfo();
	        	$response['setup_data'] = $setup_data;
	        }
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#permission')->withSuccess(trans('messages.role').' '.trans('messages.added'));		
	}

	public function update(RoleRequest $request, Role $role){

		$role->name = $request->input('name');
		$role->save();

        if($request->has('is_default')){
        	\App\Role::whereNotNull('id')->update(['is_default' => 0]);
        	$role->is_default = 1;
        	$role->save();
        }

		$this->logActivity(['module' => 'role','unique_id' => $role->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.role').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#permission')->withSuccess(trans('messages.role').' '.trans('messages.updated'));
	}

	public function destroy(Role $role,Request $request){

		if($role->is_hidden == 1){
        	if($request->has('ajax_submit')){
        		$response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	        	return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        	}
            return redirect('/configuration#permission')->withErrors(trans('messages.permission_denied'));
		}

		$this->logActivity(['module' => 'role','unique_id' => $role->id,'activity' => 'activity_deleted']);

        $role->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.role').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }
        return redirect()->back()->withSuccess(trans('messages.role').' '.trans('messages.deleted'));
	}
}
?>