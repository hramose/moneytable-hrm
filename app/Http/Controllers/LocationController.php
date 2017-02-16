<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\LocationRequest;
use Entrust;
use App\Classes\Helper;
use App\Location;
use Auth;

Class LocationController extends Controller{
    use BasicController;

	protected $form = 'location-form';

	public function index(Location $location){
		if(!Entrust::can('list_location'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$top_locations = Location::all()->pluck('name','id')->all();

        $col_heads = array(
        		trans('messages.option'),
        		trans('messages.location'),
        		trans('messages.top_location'));
        $col_heads = Helper::putCustomHeads($this->form, $col_heads);
        $table_info = array(
			'source' => 'location',
			'title' => 'Location List',
			'id' => 'location_table'
		);
		return view('location.index',compact('col_heads','table_info','top_locations'));
	}

	public function hierarchy(Request $request){

        $tree = array();
        $locations = \App\Location::all();
        foreach ($locations as $location){
            $tree[$location->id] = array(
                'parent_id' => $location->top_location_id,
                'name' => $location->name
            );
        }

        return view('location.hierarchy',compact('tree'))->render();
	}

	public function lists(Request $request){
		if(!Entrust::can('list_location'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		$locations = Location::all();

        $col_ids = Helper::getCustomColId($this->form);
        $values = Helper::fetchCustomValues($this->form);
        $rows = array();

        foreach ($locations as $location){
			$row = array(
				'<div class="btn-group btn-group-xs">'.
				(Entrust::can('edit_location') ? '<a href="#" data-href="/location/'.$location->id.'/edit" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a> ' : '').
				(Entrust::can('delete_location') ? delete_form(['location.destroy',$location->id],'location',1) : '').
				'</div>',
				$location->name,
				($location->top_location_id) ? $location->Parent->name : '<i class="fa fa-times"></i>'
			);
			$id = $location->id;

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

	public function edit(Location $location){

        if(!Entrust::can('edit_location'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$child_locations = Helper::childLocation($location->id);
		$top_locations = array_diff(Location::where('id','!=',$location->id)->get()->pluck('name','id')->all(), $child_locations);

		$custom_field_values = Helper::getCustomFieldValues($this->form,$location->id);

		return view('location.edit',compact('location','top_locations','custom_field_values'));
	}

	public function store(LocationRequest $request, Location $location){	

		if(!Entrust::can('create_location')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$data = $request->all();

		$data['top_location_id'] = ($request->input('top_location_id')) ? : null;
		$location->fill($data)->save();

		Helper::storeCustomField($this->form,$location->id, $data);

        if(\App\Setup::whereModule('location')->whereCompleted(0)->first())
        	\App\Setup::whereModule('location')->whereCompleted(0)->update(['completed' => 1]);

		$this->logActivity(['module' => 'location','unique_id' => $location->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $location->name,'id' => $location->id,'field' => 'top_location_id');
            $response = ['message' => trans('messages.location').' '.trans('messages.added'), 'status' => 'success','new_data' => $new_data]; 
	        if(config('config.application_setup_info') && defaultRole()){
	        	$setup_data = Helper::setupInfo();
	        	$response['setup_data'] = $setup_data;
	        }
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect()->back()->withSuccess(trans('messages.location').' '.trans('messages.added'));		
	}

	public function update(LocationRequest $request, Location $location){

		if(!Entrust::can('edit_location')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $data = $request->all();

		$data['top_location_id'] = ($request->input('top_location_id')) ? : null;

		$child_locations = Helper::childLocation($location->id,1);

		if($data['top_location_id'] != null && in_array($data['top_location_id'],$child_locations)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.top_location_cannot_become_child'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.top_location_cannot_become_child'));
		}

		$location->fill($data)->save();

		$this->logActivity(['module' => 'location','unique_id' => $location->id,'activity' => 'activity_updated']);

		Helper::updateCustomField($this->form,$location->id, $data);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.location').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/location')->withSuccess(trans('messages.location').' '.trans('messages.updated'));
	}

	public function destroy(Location $location,Request $request){
		if(!Entrust::can('delete_location')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$this->logActivity(['module' => 'location','unique_id' => $location->id,'activity' => 'activity_deleted']);

		Helper::deleteCustomField($this->form, $location->id);
		
        $location->delete();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.location').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/location')->withSuccess(trans('messages.location').' '.trans('messages.deleted'));
	}
}
?>