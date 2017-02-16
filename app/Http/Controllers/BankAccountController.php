<?php
namespace App\Http\Controllers;
use App\User;
use App\BankAccount;
use Entrust;
use Illuminate\Http\Request;
use App\Http\Requests\BankAccountRequest;

Class BankAccountController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $data = '';

        $employee = User::find($request->input('employee_id'));

        if(!$employee)
            return $data;

		foreach($employee->BankAccount as $bankAccount){
			$data .= '<tr>
				<td>'.$bankAccount->account_name.' '.(($bankAccount->is_primary) ? '<span class="label label-success">'.trans('messages.primary').'</span>' : '').'</td>'.
				'<td>'.$bankAccount->account_number.'</td>'.
				'<td>'.$bankAccount->bank_name.'</td>'.
				'<td>'.$bankAccount->bank_code.'</td>'.
				'<td>'.$bankAccount->bank_branch.'</td>'.
				'<td><div class="btn-group btn-group-xs">
					<a href="#" data-href="/bank-account/'.$bankAccount->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal" ><i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
					delete_form(['bank-account.destroy',$bankAccount->id]).
				'</div>
				</td>
			</tr>';
		}

		return $data;
    }

	public function store(BankAccountRequest $request, $id){
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
        	\App\BankAccount::where('user_id', $id)
          		->update(['is_primary' => 0]);

        $bank_account = new BankAccount;
	    $bank_account->fill($request->all());
        $employee->bankAccount()->save($bank_account);
        $this->logActivity(['module' => 'bank_account','unique_id' => $bank_account->id,'activity' => 'activity_added','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.bank_account').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$id."#bank-account")->withSuccess(trans('messages.bank_account').' '.trans('messages.added'));			
	}

	public function edit(BankAccount $bank_account){

		$id = $bank_account->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		return view('employee.edit_bank_account',compact('bank_account'));
	}

	public function update(BankAccountRequest $request, BankAccount $bank_account){
		$id = $bank_account->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        if($request->input('is_primary'))
        	\App\BankAccount::where('user_id', $id)
          		->update(['is_primary' => 0]);

        $bank_account->fill($request->all())->save();
        $this->logActivity(['module' => 'bank_account','unique_id' => $bank_account->id,'activity' => 'activity_updated','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.bank_account').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        return redirect('/employee/'.$id."#bank-account")->withSuccess(trans('messages.bank_account').' '.trans('messages.updated'));	
	}

	public function destroy(BankAccount $bank_account,Request $request){
		
		$id = $bank_account->User->id;
		$employee = User::find($id);
		
		if(!$this->employeeAccessible($employee)){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		if($bank_account->is_primary){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.primary_account_cannot_delete'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/employee/'.$id.'#bank-account')->withErrors(trans('messages.primary_account_cannot_delete'));
		}

        $this->logActivity(['module' => 'bank_account','unique_id' => $bank_account->id,'activity' => 'activity_deleted','secondary_id' => $id]);

		$bank_account->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.bank_account').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/employee/'.$id."#bank-account")->withSuccess(trans('messages.bank_account').' '.trans('messages.deleted'));
	}
}
?>