<?php
namespace App\Http\Controllers;
use App\User;
use App\WorkExperience;
use Entrust;
use Illuminate\Http\Request;
use App\Http\Requests\WorkExperienceRequest;

Class WorkExperienceController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $data = '';

        $employee = User::find($request->input('employee_id'));

        if(!$employee)
            return $data;

		foreach($employee->WorkExperience as $work_experience){
			$data .= '<tr>
				<td>'.$work_experience->company_name.'</td>'.
				'<td>'.showDate($work_experience->from_date).'</td>'.
				'<td>'.showDate($work_experience->to_date).'</td>'.
				'<td>'.$work_experience->post.'</td>'.
				'<td>'.$work_experience->description.'</td>';

				if(config('config.employee_manage_own_work_experience') || $request->has('show_option')){
					$data .= '<td><div class="btn-group btn-group-xs">
					<a href="#" data-href="/work-experience/'.$work_experience->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal" ><i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['work-experience.destroy',$work_experience->id]).
					'</div>
					</td>';
				}
			$data .= '</tr>';
		}

		return $data;
    }

	public function store(WorkExperienceRequest $request, $id){
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

        $work_experience = new WorkExperience;
        $data = $request->all();
        $work_experience->fill($data)->save();
        
        $employee->WorkExperience()->save($work_experience);
        $this->logActivity(['module' => 'work_experience','unique_id' => $work_experience->id,'activity' => 'activity_added','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.work_experience').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$id."#work-experience")->withSuccess(trans('messages.work_experience').' '.trans('messages.added'));			
	}

	public function edit(WorkExperience $work_experience){

		$id = $work_experience->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		return view('employee.edit_work_experience',compact('work_experience'));
	}

	public function update(WorkExperienceRequest $request, WorkExperience $work_experience){

		$id = $work_experience->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $data = $request->all();
        $work_experience->fill($data)->save();

        $this->logActivity(['module' => 'work_experience','unique_id' => $work_experience->id,'activity' => 'activity_updated','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.work_experience').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        return redirect('/employee/'.$id."#work-experience")->withSuccess(trans('messages.work_experience').' '.trans('messages.updated'));	
	}

	public function destroy(WorkExperience $work_experience,Request $request){
		
		$id = $work_experience->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $this->logActivity(['module' => 'work_experience','unique_id' => $work_experience->id,'activity' => 'activity_deleted','secondary_id' => $id]);

		$work_experience->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.work_experience').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/employee/'.$id."#work-experience")->withSuccess(trans('messages.work_experience').' '.trans('messages.deleted'));
	}
}
?>