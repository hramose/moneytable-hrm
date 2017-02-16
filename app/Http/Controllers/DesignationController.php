<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\DesignationRequest;
use Entrust;
use App\Classes\Helper;
use App\Designation;
use Auth;

Class DesignationController extends Controller{
    use BasicController;

	protected $form = 'designation-form';

	public function index(Designation $designation){

		if(!Entrust::can('list_designation'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$departments = \App\Department::all()->pluck('name','id')->all();

		$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
		array_push($child_designations, Auth::user()->designation_id);

		if(Entrust::can('manage_all_designation'))
			$top_designations = Designation::all()->pluck('full_designation','id')->all();
		elseif(Entrust::can('manage_subordinate_designation'))
			$top_designations = Designation::whereIn('id',$child_designations)->get()->pluck('full_designation','id')->all();
		else
			$top_designations = [];

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.designation'),
        		trans('messages.department'),
        		trans('messages.top_designation'));
        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $table_info = array(
			'source' => 'designation',
			'title' => 'Designation List',
			'id' => 'designation_table'
		);
		return view('designation.index',compact('col_heads','table_info','top_designations','departments'));
	}

	public function lists(Request $request){
		if(!Entrust::can('list_designation'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		if(Entrust::can('manage_all_designation'))
			$designations = Designation::all();
		elseif(Entrust::can('manage_subordinate_designation')){
			$child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
			$designations = Designation::whereIn('id',$child_designations)->get();
		} else
			$designations = [];

        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);
        $rows = array();

        foreach ($designations as $designation){
			$row = array(
				'<div class="btn-group btn-group-xs">'.
				(Entrust::can('edit_designation') ? '<a href="#" data-href="/designation/'.$designation->id.'/edit" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
				(Entrust::can('delete_designation') ? delete_form(['designation.destroy',$designation->id],'designation',1) : '').
				'</div>',
				$designation->name.' '.(($designation->is_hidden) ? '<span class="label label-danger">'.trans('messages.default').'</span>' : ''),
				$designation->Department->name,
				($designation->top_designation_id) ? $designation->Parent->full_designation : '<i class="fa fa-times"></i>'
			);
			$id = $designation->id;

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
	}

	public function edit(Designation $designation){

        if(!$this->designationAccessible($designation) || !Entrust::can('edit_designation'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$child_designations = Helper::childDesignation($designation->id);
		array_push($child_designations, Auth::user()->designation_id);

		// array_diff is used to remove child designations from the lists.

		if(Entrust::can('manage_all_designation'))
			$top_designations = array_diff(Designation::where('id','!=',$designation->id)->get()->pluck('full_designation','id')->all(), $child_designations);
		elseif(Entrust::can('manage_subordinate_designation'))
			$top_designations = array_diff(Designation::where('id','!=',$designation->id)->whereIn('id',$child_designations)->get()->pluck('full_designation','id')->all(), $child_designations);
		else
			$top_designations = [];

		$departments = \App\Department::all()->pluck('name','id')->all();

		$custom_field_values = Helper::getCustomFieldValues($this->form,$designation->id);

		return view('designation.edit',compact('designation','top_designations','custom_field_values','departments'));
	}

	public function store(DesignationRequest $request, Designation $designation){	

		if(!Entrust::can('create_designation')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$data = $request->all();

		$data['top_designation_id'] = ($request->input('top_designation_id')) ? : null;
		$designation->fill($data)->save();

		Helper::storeCustomField($this->form,$designation->id, $data);

		$this->logActivity(['module' => 'designation','unique_id' => $designation->id,'activity' => 'activity_added']);

        if(\App\Setup::whereModule('designation')->whereCompleted(0)->first())
        	\App\Setup::whereModule('designation')->whereCompleted(0)->update(['completed' => 1]);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $designation->full_designation,'id' => $designation->id,'field' => 'top_designation_id');
            $response = ['message' => trans('messages.designation').' '.trans('messages.added'), 'status' => 'success','new_data' => $new_data]; 
	        if(config('config.application_setup_info') && defaultRole()){
	        	$setup_data = Helper::setupInfo();
	        	$response['setup_data'] = $setup_data;
	        }
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.designation').' '.trans('messages.added'));		
	}

	public function update(DesignationRequest $request, Designation $designation){

		if(!Entrust::can('edit_designation') || !$this->designationAccessible($designation)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $data = $request->all();

		$data['top_designation_id'] = ($request->input('top_designation_id')) ? : null;

		$child_designations = Helper::childDesignation($designation->id,1);

		if($data['top_designation_id'] != null && in_array($data['top_designation_id'],$child_designations)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.top_designation_cannot_become_child'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.top_designation_cannot_become_child'));
		}

		$designation->fill($data)->save();

		$this->logActivity(['module' => 'designation','unique_id' => $designation->id,'activity' => 'activity_updated']);

		Helper::updateCustomField($this->form,$designation->id, $data);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.designation').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/designation')->withSuccess(trans('messages.designation').' '.trans('messages.updated'));
	}

	public function destroy(Designation $designation,Request $request){
		if(!Entrust::can('delete_designation') || !$this->designationAccessible($designation) || $designation->is_hidden == 1){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$this->logActivity(['module' => 'designation','unique_id' => $designation->id,'activity' => 'activity_deleted']);

		Helper::deleteCustomField($this->form, $designation->id);
		
        $designation->delete();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.designation').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/designation')->withSuccess(trans('messages.designation').' '.trans('messages.deleted'));
	}
}
?>