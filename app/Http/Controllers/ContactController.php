<?php
namespace App\Http\Controllers;
use App\User;
use App\Contact;
use Entrust;
use App\Classes\Helper;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;

Class ContactController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $data = '';

        $employee = User::find($request->input('employee_id'));

        if(!$employee)
            return $data;

		foreach($employee->Contact as $contact){
			$data .= '<tr>
				<td><a href="#" data-href="/contact/'.$contact->id.'" data-toggle="modal" data-target="#myModal">'.$contact->name.'</a> '.(($contact->is_primary) ? '<span class="label label-success">'.trans('messages.primary').'</span>' : '').' '.(($contact->is_dependent) ? '<span class="label label-danger">'.trans('messages.dependent').'</span>' : '').'</td>
				<td>'.trans('messages.'.$contact->relation).'</td>
				<td>'.$contact->work_email.'</td>
				<td>'.$contact->mobile.'</td>';

				if(config('config.employee_manage_own_contact') || $request->has('show_option')){
					$data .= '<td>
						<div class="btn-group btn-group-xs">
						<a href="#" data-href="/contact/'.$contact->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"> <i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
						delete_form(['contact.destroy',$contact->id],'employee_contact','1').'
						</div>
					</td>';
				}
			$data .= '</tr>';
		}
		return $data;
    }

	public function store(ContactRequest $request, $id){
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

        if($request->input('is_primary'))
        	\App\Contact::where('user_id', $id)
          		->update(['is_primary' => 0]);

        $contact = new Contact;
        $data = $request->all();
        $data['is_dependent'] = ($request->has('is_dependent')) ? 1 : 0;
        $data['is_primary'] = ($request->has('is_primary')) ? 1 : 0;
	    $contact->fill($data);
        $employee->contact()->save($contact);

        $this->logActivity(['module' => 'contact','unique_id' => $contact->id,'activity' => 'activity_added','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.contact').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$id."#contact")->withSuccess(trans('messages.contact').' '.trans('messages.added'));			
	}

	public function show(Contact $contact){

		$id = $contact->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		return view('employee.view_contact',compact('contact'));
	}

	public function edit(Contact $contact){

		$id = $contact->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        $employee_relation = Helper::translateList(config('lists.employee_relation'));

		return view('employee.edit_contact',compact('contact','employee_relation'));
	}

	public function update(ContactRequest $request, Contact $contact){
		$id = $contact->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        if($request->has('is_primary'))
        	\App\Contact::where('user_id', $id)
          		->update(['is_primary' => 0]);

        $data = $request->all();
        $data['is_dependent'] = ($request->has('is_dependent')) ? 1 : 0;
        $data['is_primary'] = ($request->has('is_primary')) ? 1 : 0;
        $contact->fill($data)->save();

        $this->logActivity(['module' => 'contact','unique_id' => $contact->id,'activity' => 'activity_updated','secondary_id' => $employee->id]);


        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.contact').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$id."#contact")->withSuccess(trans('messages.contact').' '.trans('messages.updated'));	
	}


	public function destroy(Contact $contact,Request $request){
		
		$id = $contact->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		if($contact->is_primary){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.primary_contact_cannot_delete'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/employee/'.$id.'#contact')->withErrors(trans('messages.primary_contact_cannot_delete'));
		}

        $this->logActivity(['module' => 'contact','unique_id' => $contact->id,'activity' => 'activity_deleted','secondary_id' => $id]);

		$contact->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.contact').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/employee/'.$id."#contact")->withSuccess(trans('messages.contact').' '.trans('messages.deleted'));
	}
}