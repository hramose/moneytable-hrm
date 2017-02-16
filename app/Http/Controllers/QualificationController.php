<?php
namespace App\Http\Controllers;
use App\User;
use App\Qualification;
use Entrust;
use Illuminate\Http\Request;
use App\Http\Requests\QualificationRequest;

Class QualificationController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $data = '';

        $employee = User::find($request->input('employee_id'));

        if(!$employee)
            return $data;

		foreach($employee->Qualification as $qualification){
			$data .= '<tr>
				<td>'.$qualification->institute_name.'</td>'.
				'<td>'.$qualification->from_year.'</td>'.
				'<td>'.$qualification->to_year.'</td>'.
				'<td>'.(($qualification->education_level_id) ? $qualification->EducationLevel->name : '').'</td>'.
				'<td>'.(($qualification->qualification_language_id) ? $qualification->QualificationLanguage->name : '').'</td>'.
				'<td>'.(($qualification->qualification_skill_id) ? $qualification->QualificationSkill->name : '').'</td>';
				if(config('config.employee_manage_own_qualification') || $request->has('show_option')){
					$data .= '<td><div class="btn-group btn-group-xs">
					<a href="#" data-href="/qualification/'.$qualification->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal" ><i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['qualification.destroy',$qualification->id]).
						'</div>
					</td>';
				}
			$data .= '</tr>';
		}

		return $data;
    }

	public function store(QualificationRequest $request, $id){

        $employee = User::find($id);

        if(!$employee){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
            return redirect('/employee')->withErrors(trans('messages.invalid_link'));
        }

		if(!$this->employeeAccessible($employee)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$count = Qualification::whereUserId($id)->whereInstituteName($request->input('institute_name'))->whereFromYear($request->input('from_year'))->whereToYear($request->input('to_year'))->count();

		if($count){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.duplicate_entry',['attr1' => $request->input('name'),'attr2' => $employee->full_name]), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.duplicate_entry',['attr1' => $request->input('name'),'attr2' => $employee->full_name]));
		}

        $qualification = new Qualification;
        $data = $request->all();
		$data['education_level_id'] = ($request->input('education_level_id')) ? : null;
		$data['qualification_language_id'] = ($request->input('qualification_language_id')) ? : null;
		$data['qualification_skill_id'] = ($request->input('qualification_skill_id')) ? : null;

	    $qualification->fill($data);
        $employee->qualification()->save($qualification);
        $this->logActivity(['module' => 'qualification','unique_id' => $qualification->id,'activity' => 'activity_added','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.qualification').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$id."#qualification")->withSuccess(trans('messages.qualification').' '.trans('messages.added'));			
	}

	public function edit(Qualification $qualification){

		$id = $qualification->User->id;
		$employee = User::find($id);
        $education_levels = \App\EducationLevel::pluck('name','id')->all();
        $qualification_languages = \App\QualificationLanguage::pluck('name','id')->all();
        $qualification_skills = \App\QualificationSkill::pluck('name','id')->all();
		
		if(!$this->employeeAccessible($employee))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		return view('employee.edit_qualification',compact('qualification','education_levels','qualification_languages','qualification_skills'));
	}

	public function update(QualificationRequest $request, Qualification $qualification){

		$id = $qualification->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		$count = Qualification::where('id','!=',$qualification->id)->whereUserId($id)->whereInstituteName($request->input('institute_name'))->whereFromYear($request->input('from_year'))->whereToYear($request->input('to_year'))->count();

		if($count){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.duplicate_entry',['attr1' => $request->input('name'),'attr2' => $employee->full_name]), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withErrors(trans('messages.duplicate_entry',['attr1' => $request->input('name'),'attr2' => $employee->full_name]));
		}

		$data = $request->all();
		$data['education_level_id'] = ($request->input('education_level_id')) ? : null;
		$data['qualification_language_id'] = ($request->input('qualification_language_id')) ? : null;
		$data['qualification_skill_id'] = ($request->input('qualification_skill_id')) ? : null;
        $qualification->fill($data)->save();
        $this->logActivity(['module' => 'qualification','unique_id' => $qualification->id,'activity' => 'activity_updated','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.qualification').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        return redirect('/employee/'.$id."#qualification")->withSuccess(trans('messages.qualification').' '.trans('messages.updated'));	
	}

	public function destroy(Qualification $qualification,Request $request){
		
		$id = $qualification->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $this->logActivity(['module' => 'qualification','unique_id' => $qualification->id,'activity' => 'activity_deleted','secondary_id' => $id]);

		$qualification->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.qualification').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/employee/'.$id."#qualification")->withSuccess(trans('messages.qualification').' '.trans('messages.deleted'));
	}
}
?>