<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\QualificationSkillRequest;
use App\QualificationSkill;
use App\Classes\Helper;

Class QualificationSkillController extends Controller{
    use BasicController;

	public function index(){
	}

	public function show(){
	}

	public function create(){
		return view('qualification_skill.create');
	}

	public function lists(){
		$qualification_skills = QualificationSkill::all();

		$data = '';
		foreach($qualification_skills as $qualification_skill){
			$data .= '<tr>
				<td>'.$qualification_skill->name.'</td>
				<td>
					<div class="btn-group btn-group-xs">
					<a href="#" data-href="/qualification-skill/'.$qualification_skill->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['qualification-skill.destroy',$qualification_skill->id],'qualification_skill','1').'
					</div>
				</td>
			</tr>';
		}
		return $data;
	}

	public function edit(QualificationSkill $qualification_skill){
		return view('qualification_skill.edit',compact('qualification_skill'));
	}

	public function store(QualificationSkillRequest $request, QualificationSkill $qualification_skill){
		$qualification_skill->fill($request->all())->save();

		$this->logActivity(['module' => 'qualification_skill','unique_id' => $qualification_skill->id,'activity' => 'activity_added']);

        if($request->has('ajax_submit')){
        	$new_data = array('value' => $qualification_skill->name,'id' => $qualification_skill->id,'field' => 'qualification_skill_id');
        	$data = $this->lists();
            $response = ['message' => trans('messages.qualification_skill').' '.trans('messages.added'), 'status' => 'success','data' => $data,'new_data' => $new_data]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#qualification')->withSuccess(trans('messages.qualification_skill').' '.trans('messages.added'));
	}

	public function update(QualificationSkillRequest $request, QualificationSkill $qualification_skill){

		$qualification_skill->fill($request->all())->save();

		$this->logActivity(['module' => 'qualification_skill','unique_id' => $qualification_skill->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
        	$data = $this->lists();
	        $response = ['message' => trans('messages.qualification_skill').' '.trans('messages.updated'), 'status' => 'success','data' => $data]; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#qualification')->withSuccess(trans('messages.qualification_skill').' '.trans('messages.updated'));
	}

	public function destroy(QualificationSkill $qualification_skill,Request $request){
		$this->logActivity(['module' => 'qualification_skill','unique_id' => $qualification_skill->id,'activity' => 'activity_deleted']);

        $qualification_skill->delete();

        if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.qualification_skill').' '.trans('messages.deleted'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

        return redirect()->back()->withSuccess(trans('messages.qualification_skill').' '.trans('messages.deleted'));
	}
}
?>