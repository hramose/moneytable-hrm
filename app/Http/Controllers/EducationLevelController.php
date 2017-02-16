<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\EducationLevelRequest;
use App\EducationLevel;
use App\Classes\Helper;

Class EducationLevelController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('education_level.create');
	}

	public function lists(){
		$education_levels = EducationLevel::all();

		$data = '';
		foreach($education_levels as $education_level){
			$data .= '<tr>
				<td>'.$education_level->name.'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/education-level/'.$education_level->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['education-level.destroy',$education_level->id],'education_level','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(EducationLevel $education_level){
		return view('education_level.edit',compact('education_level'));
	}

	public function store(EducationLevelRequest $request, EducationLevel $education_level){
		$education_level->fill($request->all())->save();

		$this->logActivity(['module' => 'education_level','unique_id' => $education_level->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $education_level->name,'id' => $education_level->id,'field' => 'education_level_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.education_level').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#qualification')->withSuccess(trans('messages.education_level').' '.trans('messages.added'));
	}

	public function update(EducationLevelRequest $request, EducationLevel $education_level){

		$education_level->fill($request->all())->save();

		$this->logActivity(['module' => 'education_level','unique_id' => $education_level->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.education_level').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#qualification')->withSuccess(trans('messages.education_level').' '.trans('messages.updated'));
	}

	public function destroy(EducationLevel $education_level,Request $request){
		$this->logActivity(['module' => 'education_level','unique_id' => $education_level->id,'activity' => 'activity_deleted']);

        $education_level->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.education_level').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect()->back()->withSuccess(trans('messages.education_level').' '.trans('messages.deleted'));
	}
}
?>