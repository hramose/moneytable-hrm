<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\SalaryRequest;
use App\SalaryType;
use App\Salary;
use Validator;

Class SalaryController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $data = '';

        $employee = \App\User::find($request->input('employee_id'));

        if(!$employee)
            return $data;

        $contracts = \App\Contract::whereUserId($employee->id)->pluck('id')->all();
        $contract_salaries = \App\Salary::whereIn('contract_id',$contracts)->groupBy('contract_id')->get();
        $salaries = \App\Salary::whereIn('contract_id',$contracts)->get();
        $earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
        $deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();

		foreach($contract_salaries as $contract_salary){
			$data .= '<tr>
				<td>'.$contract_salary->Contract->full_contract_detail.'</td>
                <td>'.(($contract_salary->Contract->hourly_payroll) ? currency($contract_salary->Contract->hourly_rate).' <span class="label label-danger">'.trans('messages.hourly').'</span>' : '-').'</td>';

                if(!$contract_salary->Contract->hourly_payroll) {
                    $data .= '<td>'.currency($contract_salary->Contract->overtime_hourly_rate).'</td>
                    <td>'.currency($contract_salary->Contract->late_hourly_rate).'</td>
                    <td>'.currency($contract_salary->Contract->early_leaving_hourly_rate).'</td>';
                } else {
                    $data .= '<td colspan="3">-</td>';
                }


                if(!$contract_salary->Contract->hourly_payroll)
				foreach($earning_salary_types as $earning_salary_type){
				    $data .= '<td>'.
					(($salaries->whereLoose('contract_id',$contract_salary->contract_id)->whereLoose('salary_type_id',$earning_salary_type->id)->first()) ? currency($salaries->whereLoose('contract_id',$contract_salary->contract_id)->whereLoose('salary_type_id',$earning_salary_type->id)->first()->amount) : 0).'</td>';
				} else {
                    $data .= '<td colspan="'.count($earning_salary_types).'">-</td>';
                }

                if(!$contract_salary->Contract->hourly_payroll)
				foreach($deduction_salary_types as $deduction_salary_type){
				$data .= '<td>'.
					(($salaries->whereLoose('contract_id',$contract_salary->contract_id)->whereLoose('salary_type_id',$deduction_salary_type->id)->first()) ? currency($salaries->whereLoose('contract_id',$contract_salary->contract_id)->whereLoose('salary_type_id',$deduction_salary_type->id)->first()->amount) : 0).'</td>';
				} else {
                    $data .= '<td colspan="'.count($deduction_salary_types).'">-</td>';
                }
				$data .= '<td>
						<div class="btn-group btn-group-xs">
							<a href="#" data-href="/salary/'.$contract_salary->contract_id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
							delete_form(['salary.destroy',$contract_salary->contract_id]).
						'</div>
					</td>
				</tr>';
		}

		return $data;

    }

	public function store(Request $request,$id){
        $employee = \App\User::find($id);

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

        $validation = Validator::make($request->all(),[
            'salary_contract_id' => 'required|unique:salary,contract_id',
            'hourly_rate' => 'required_if:hourly_payroll,1|numeric',
            'overtime_hourly_rate' => 'numeric',
            'late_hourly_rate' => 'numeric',
            'early_leaving_hourly_rate' => 'numeric',
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages()->first());
        }

		$salary_types = SalaryType::all();

        $contract = \App\Contract::find($request->input('salary_contract_id'));

        $contract->late_hourly_rate =  (!$request->has('hourly_payroll')) ? (($request->input('late_hourly_rate')) ? : 0) : 0;
        $contract->early_leaving_hourly_rate =  (!$request->has('hourly_payroll')) ? (($request->input('early_leaving_hourly_rate')) ? : 0) : 0;
        $contract->overtime_hourly_rate =  (!$request->has('hourly_payroll')) ? (($request->input('overtime_hourly_rate')) ? : 0) : 0;
        $contract->hourly_payroll = ($request->has('hourly_payroll')) ? 1 : 0;
        $contract->hourly_rate = ($request->has('hourly_payroll')) ? (($request->input('hourly_rate')) ? : 0) : 0;
        $contract->save();

		foreach($salary_types as $salary_type){
			$salary = new Salary;
			$salary->contract_id = $request->input('salary_contract_id');
			$salary->salary_type_id = $salary_type->id;
			$salary->amount = (!$request->has('hourly_payroll')) ? (($request->input($salary_type->id)) ? : 0) : 0;
			$salary->save();
		}
        $this->logActivity(['module' => 'salary','activity' => 'activity_added','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.salary').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/employee/'.$id.'/#salary')->withSuccess(trans('messages.employee').' '.trans('messages.salary').' '.trans('messages.added'));
	}

	public function edit($id){
		$contract = \App\Contract::find($id);

        if(!$contract)
            return view('common.error',['message' => trans('messages.invalid_link')]);

		$employee = $contract->User;

		if(!$this->employeeAccessible($employee))
            return view('common.error',['message' => trans('messages.permission_denied')]);

		$salaries = Salary::whereContractId($id)->get()->pluck('amount','salary_type_id')->all();

        $earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
        $deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();
		return view('employee.edit_salary',compact('salaries','contract','earning_salary_types','deduction_salary_types'));
	}

	public function update(Request $request, $id){
		$contract = \App\Contract::find($id);

        if(!$contract){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee')->withErrors(trans('messages.invalid_link'));
        }

		$employee = $contract->User;

		if(!$this->employeeAccessible($employee)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

        $contract->late_hourly_rate =  (!$request->has('hourly_payroll')) ? (($request->input('late_hourly_rate')) ? : 0) : 0;
        $contract->early_leaving_hourly_rate =  (!$request->has('hourly_payroll')) ? (($request->input('early_leaving_hourly_rate')) ? : 0) : 0;
        $contract->overtime_hourly_rate =  (!$request->has('hourly_payroll')) ? (($request->input('overtime_hourly_rate')) ? : 0) : 0;
        $contract->hourly_payroll = ($request->has('hourly_payroll')) ? 1 : 0;
        $contract->hourly_rate = ($request->has('hourly_payroll')) ? (($request->input('hourly_rate')) ? : 0) : 0;
        $contract->save();

		$salary_types = SalaryType::all();

		foreach($salary_types as $salary_type){
			$salary = Salary::firstOrNew(array('contract_id' => $id, 'salary_type_id' => $salary_type->id));
			$salary->contract_id = $id;
            $salary->amount = (!$request->has('hourly_payroll')) ? (($request->input($salary_type->id)) ? : 0) : 0;
			$salary->save();
		}

        $this->logActivity(['module' => 'salary','activity' => 'activity_updated','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.salary').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/employee/'.$employee->id.'/#salary')->withSuccess(trans('messages.employee').' '.trans('messages.salary').' '.trans('messages.updated'));
	}

	public function destroy($id,Request $request){
		$contract = \App\Contract::find($id);

        if(!$contract){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee')->withErrors(trans('messages.invalid_link'));
        }

		$employee = $contract->User;

		if(!$this->employeeAccessible($employee)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
		}

		\App\Salary::whereContractId($id)->delete();
        $this->logActivity(['module' => 'salary','activity' => 'activity_deleted','secondary_id' => $employee->id]);
        
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.employee').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/employee/'.$employee->id.'/#salary')->withSuccess(trans('messages.employee').' '.trans('messages.salary').' '.trans('messages.deleted'));
	}
}
?>