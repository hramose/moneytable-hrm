<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\AwardTypeRequest;
use App\AwardType;
use App\Classes\Helper;

Class AwardTypeController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('award_type.create');
	}

	public function lists(){
		$award_types = AwardType::all();

		$data = '';
		foreach($award_types as $award_type){
			$data .= '<tr>
				<td>'.$award_type->name.'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/award-type/'.$award_type->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['award-type.destroy',$award_type->id],'award_type','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(AwardType $award_type){
		return view('award_type.edit',compact('award_type'));
	}

	public function store(AwardTypeRequest $request, AwardType $award_type){
		$award_type->fill($request->all())->save();

		$this->logActivity(['module' => 'award_type','unique_id' => $award_type->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $award_type->name,'id' => $award_type->id,'field' => 'award_type_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.award_type').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#award-type')->withSuccess(trans('messages.award_type').' '.trans('messages.added'));
	}

	public function update(AwardTypeRequest $request, AwardType $award_type){

		$award_type->fill($request->all())->save();

		$this->logActivity(['module' => 'award_type','unique_id' => $award_type->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.award_type').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#award-type')->withSuccess(trans('messages.award_type').' '.trans('messages.updated'));
	}

	public function destroy(AwardType $award_type,Request $request){
		$this->logActivity(['module' => 'award_type','unique_id' => $award_type->id,'activity' => 'activity_deleted']);

        $award_type->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.award_type').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect()->back()->withSuccess(trans('messages.award_type').' '.trans('messages.deleted'));
	}
}
?>