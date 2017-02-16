<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\DepartmentRequest;
use Entrust;
use App\Classes\Helper;
use App\Department;

Class DepartmentController extends Controller{
    use BasicController;

	protected $form = 'department-form';

	public function index(Department $department){

		if(!Entrust::can('list_department'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.department_name'),
        		trans('messages.description'),
        		trans('messages.designation'),
        		trans('messages.total_employee')
        		);

        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $table_info = array(
			'source' => 'department',
			'title' => 'Department List',
			'id' => 'department_table'
		);
		return view('department.index',compact('col_heads','table_info'));
	}

	public function lists(Request $request){
		if(!Entrust::can('list_department'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$departments = Department::all();
        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);
        $rows = array();

        foreach($departments as $department){

        	$total_employee = 0;
        	$designation_list = '<ol>';
        	foreach($department->Designation as $designation){
        		$designation_list .= '<li>'.$designation->name.'</li>';
        		$total_employee += $designation->hasMany('\App\User')->count();
        	}
        	$designation_list .= '</ol>';

			$row = array(
				'<div class="btn-group btn-group-xs">'.
				(Entrust::can('edit_department') ? '<a href="#" data-href="/department/'.$department->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
				(Entrust::can('delete_department') ? delete_form(['department.destroy',$department->id],'department',1) : '').
				'</div>',
				$department->name.' '.(($department->is_hidden) ? '<span class="label label-danger">'.trans('messages.default').'</span>' : ''),
				$department->description,
				$designation_list,
				$total_employee
				);
			$id = $department->id;

			foreach($col_ids as $col_id)
				array_push($row,isset($values[$id][$col_id]) ? $values[$id][$col_id] : '');
			$rows[] = $row;
        }
        $list['aaData'] = $rows;
        return json_encode($list);
	}

	public function show(){
	}

	public function create(){

		if(!Entrust::can('create_department'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        return view('department.create');
	}

	public function edit(Department $department){

		if(!Entrust::can('edit_department'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$custom_field_values = Helper::getCustomFieldValues($this->form,$department->id);
		return view('department.edit',compact('department','custom_field_values'));
	}

	public function store(DepartmentRequest $request, Department $department){	

		if(!Entrust::can('create_department')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$data = $request->all();
		$department->fill($data)->save();
		Helper::storeCustomField($this->form,$department->id, $data);

		$this->logActivity(['module' => 'department','unique_id' => $department->id,'activity' => 'activity_added']);

        if(\App\Setup::whereModule('department')->whereCompleted(0)->first())
        	\App\Setup::whereModule('department')->whereCompleted(0)->update(['completed' => 1]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.department').' '.trans('messages.added'), 'status' => 'success']; 
	        if(config('config.application_setup_info') && defaultRole()){
	        	$setup_data = Helper::setupInfo();
	        	$response['setup_data'] = $setup_data;
	        }
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.department').' '.trans('messages.added'));		
	}

	public function update(DepartmentRequest $request, Department $department){

		if(!Entrust::can('edit_department')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$data = $request->all();
		$department->fill($data)->save();

		$this->logActivity(['module' => 'department','unique_id' => $department->id,'activity' => 'activity_updated']);

		Helper::updateCustomField($this->form,$department->id, $data);
		
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.department').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/department')->withSuccess(trans('messages.department').' '.trans('messages.updated'));
	}

	public function destroy(Department $department,Request $request){
		if(!Entrust::can('delete_department') || $department->is_hidden == 1){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$this->logActivity(['module' => 'department','unique_id' => $department->id,'activity' => 'activity_deleted']);

		Helper::deleteCustomField($this->form, $department->id);
        
        $department->delete();
        
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.department').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        return redirect('/department')->withSuccess(trans('messages.department').' '.trans('messages.deleted'));
	}
}
?>