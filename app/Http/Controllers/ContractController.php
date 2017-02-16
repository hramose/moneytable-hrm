<?php
namespace App\Http\Controllers;
use App\Classes\Helper;
use Config;
use App\Contract;
use Auth;
use DB;
use Entrust;
use Illuminate\Http\Request;
use App\Http\Requests\ContractRequest;

Class ContractController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $data = '';

        $employee = \App\User::find($request->input('employee_id'));

        if(!$employee)
            return $data;

        foreach($employee->Contract as $contract){
            $data .= '<tr>
                <td>'.showDate($contract->from_date).' to '.showDate($contract->to_date).' '.
                    ((date('Y-m-d') >= $contract->from_date && date('Y-m-d') <= $contract->to_date) ? '<span class="label label-success">'.trans('messages.active').'</span>' : '').'</td>'.
                '<td>'.$contract->Designation->full_designation.'</td>'.
                '<td>'.$contract->title.'</td>'.
                '<td>'.$contract->ContractType->name.'</td>'.
                '<td>
                    <div class="btn-group btn-group-xs">
                        <a href="#" data-href="/contract/'.$contract->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
                        delete_form(['contract.destroy',$contract->id]).
                    '</div>
                </td>
            </tr>';
        }

        return $data;
    }

    public function store(ContractRequest $request, $id){
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

        $contracts = Contract::whereUserId($id)
            ->where(function ($query) use($request) { $query->where(function ($query) use($request){
                $query->where('from_date','<=',$request->input('from_date'))
                ->where('to_date','>=',$request->input('from_date'));
                })->orWhere(function ($query) use($request) {
                    $query->where('from_date','<=',$request->input('to_date'))
                        ->where('to_date','>=',$request->input('to_date'));
                });})->count();

        if($contracts){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.contract_already_signed'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee/'.$id.'#contract')->withErrors(trans('messages.contract_already_signed'));
        }

        $contract = new Contract;
        $data = $request->all();
        $data['user_id'] = $id;
        $data['designation_id'] = $request->input('new_designation_id');

        if($request->input('from_date') <= date('Y-m-d') && $request->input('to_date') >= date('Y-m-d')){
            $employee->designation_id = $request->input('new_designation_id');
            $employee->save();
        }

        $contract->fill($data)->save();

        $user = \App\User::find($contract->User->id);
        $user_contract = Helper::getContract($user->id);
        if($user_contract && isset($user_contract->designation_id)){
            $user->designation_id = $user_contract->designation_id;
            $user->save();
        }

        $this->logActivity(['module' => 'contract','unique_id' => $contract->id,'activity' => 'activity_added','secondary_id' => $employee->id]);

        if(\App\Setup::whereModule('contract')->whereCompleted(0)->first())
            \App\Setup::whereModule('contract')->whereCompleted(0)->update(['completed' => 1]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.contract').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        return redirect('/employee/'.$id.'#contract')->withSuccess(trans('messages.employee').' '.trans('messages.contract').' '.trans('messages.added'));
    }

    public function edit(Contract $contract){
        $employee = \App\User::find($contract->user_id);

        if(!$this->employeeAccessible($employee))
            return view('common.error',['message' => trans('messages.permission_denied')]);
        
        $contract_types = \App\ContractType::pluck('name','id')->all();

        if(Entrust::can('manage_all_employee'))
            $designations = \App\Designation::all()->pluck('full_designation','id')->all();
        elseif(Entrust::can('manage_subordinate_employee')) {
            $childs = Helper::childDesignation(Auth::user()->designation_id);
            $designations = \App\Designation::whereIn('id',$childs)->get()->pluck('full_designation','id')->all();
        } else
            $designations = [];

        return view('employee.edit_contract',compact('contract_types','employee','contract','designations'));
    }

    public function update(ContractRequest $request, Contract $contract){
        if(!$this->employeeAccessible($contract->User)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
        }

        $contracts = Contract::whereUserId($contract->user_id)
            ->where('id','!=',$contract->id)
            ->where(function ($query) use($request) { $query->where(function ($query) use($request){
                $query->where('from_date','<=',$request->input('from_date'))
                ->where('to_date','>=',$request->input('from_date'));
                })->orWhere(function ($query) use($request) {
                    $query->where('from_date','<=',$request->input('to_date'))
                        ->where('to_date','>=',$request->input('to_date'));
                });})->count();

        if($contracts){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.contract_already_signed'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee/'.$contract->user_id.'#contract')->withErrors(trans('messages.contract_already_signed'));
        }

        $data = $request->all();
        $data['designation_id'] = $request->input('new_designation_id');
        $contract->fill($data)->save();

        if($request->input('from_date') <= date('Y-m-d') && $request->input('to_date') >= date('Y-m-d')){
            $employee = $contract->User;
            $employee->designation_id = $request->input('new_designation_id');
            $employee->save();
        }

        $user = \App\User::find($contract->User->id);
        $user_contract = Helper::getContract($user->id);
        if($user_contract && isset($user_contract->designation_id)){
            $user->designation_id = $user_contract->designation_id;
            $user->save();
        }
        $this->logActivity(['module' => 'contract','unique_id' => $contract->id,'activity' => 'activity_updated','secondary_id' => $contract->user_id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.contract').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        return redirect('/employee/'.$contract->user_id.'#contract')->withSuccess(trans('messages.employee').' '.trans('messages.contract').' '.trans('messages.updated'));
    }

    public function destroy(Contract $contract,Request $request){
        if(!$this->employeeAccessible($contract->User)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
        }

        $leave = \App\Leave::whereUserId($contract->user_id)
            ->where('from_date','>=',$contract->from_date)
            ->where('to_date','<=',$contract->to_date)
            ->get();

        if(count($leave)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.leave_already_approved_for_contract'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.leave_already_approved_for_contract'));
        }

        $this->logActivity(['module' => 'contract','unique_id' => $contract->id,'activity' => 'activity_deleted','secondary_id' => $contract->User->id]);
        $contract->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.contract').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.employee').' '.trans('messages.contract').' '.trans('messages.deleted'));

    }
}
?>