<?php
namespace App\Http\Controllers;
use App\Classes\Helper;
use App\UserLeave;
use Auth;
use Illuminate\Http\Request;
use Validator;

Class UserLeaveController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $data = '';

        $employee = \App\User::find($request->input('employee_id'));

        if(!$employee)
            return $data;

        $leave_types = \App\LeaveType::all();
        $contracts = \App\Contract::whereUserId($employee->id)->pluck('id')->all();
        $contract_leaves = \App\UserLeave::whereIn('contract_id',$contracts)->groupBy('contract_id')->get();
        $leaves = \App\UserLeave::whereIn('contract_id',$contracts)->get();

        foreach($contract_leaves as $contract_leave){
        $data .= '<tr>
            <td>'.$contract_leave->Contract->full_contract_detail.'</td>';
            foreach($leave_types as $leave_type){
                $data .= '<td>'.(($leaves->whereLoose('contract_id',$contract_leave->contract_id)->whereLoose('leave_type_id',$leave_type->id)->first()) ? $leaves->whereLoose('contract_id',$contract_leave->contract_id)->whereLoose('leave_type_id',$leave_type->id)->first()->leave_count : 0).'</td>';
            }
        $data .= '<td>
                <div class="btn-group btn-group-xs">
                    <a href="#" data-href="/user-leave/'.$contract_leave->contract_id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
                    delete_form(['user-leave.destroy',$contract_leave->contract_id]).
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
            'leave_contract_id' => 'required|unique:user_leaves,contract_id'
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages()->first());
        }

        $contract = \App\Contract::find($request->input('leave_contract_id'));
        $duration = abs((strtotime($contract->from_date) - strtotime($contract->to_date)) / (60*60*24)) + 1;
        
        $total_leaves = 0;
        $leave_types = $request->input('leave');
        foreach($leave_types as $key => $leave)
            $total_leaves += $leave;

        if($total_leaves > $duration){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.exceed_leave_in_contract_period'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.exceed_leave_in_contract_period'));
        }

        foreach($leave_types as $key => $leave){
            $user_leave = new UserLeave;
            $user_leave->contract_id = $request->input('leave_contract_id');
            $user_leave->leave_type_id = $key;
            $user_leave->leave_count = $leave;
            $user_leave->save();
        }
        $this->logActivity(['module' => 'leave','activity' => 'activity_updated','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.leave').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.employee').' '.trans('messages.leave').' '.trans('messages.added'));
    }

    public function edit($id){
        $contract = \App\Contract::find($id);

        if(!$contract)
            return view('common.error',['message' => trans('messages.invalid_link')]);

        if(!$this->employeeAccessible($contract->User))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        $leaves = UserLeave::whereContractId($id)->get()->pluck('leave_count','leave_type_id')->all();

        $leave_types = \App\LeaveType::all();

        return view('employee.edit_leave',compact('contract','leaves','leave_types'));
    }

    public function update(Request $request, $id){

        $contract = \App\Contract::find($id);

        if(!$contract){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.invalid_link'));
        }

        if(!$this->employeeAccessible($contract->User)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
        }

        $leave_uses = \App\Leave::whereUserId($contract->user_id)
            ->whereStatus('approved')
            ->where('from_date','>=',$contract->from_date)
            ->where('to_date','<=',$contract->to_date)
            ->get();

        $leave_types = \App\LeaveType::all();
        $used = [];
        foreach($leave_types as $leave_type)
           $used[$leave_type->id] = 0;

        foreach($leave_uses as $leave_use)
            $used[$leave_use->leave_type_id] = (strtotime($leave_use->to_date) - strtotime($leave_use->from_date)) / (60*60*24) + 1;

        $leave = $request->input('leave');
        foreach($leave_types as $leave_type)
            if($used[$leave_type->id] > $leave[$leave_type->id]){
                if($request->has('ajax_submit')){
                    $response = ['message' => trans('messages.employee_already_used_leave'), 'status' => 'error']; 
                    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
                }
                return redirect()->back()->withErrors(trans('messages.employee_already_used_leave'));
            }

        foreach($leave_types as $leave_type){
            $user_leave = UserLeave::whereContractId($id)->whereLeaveTypeId($leave_type->id)->first();

            if(!$user_leave)
                $user_leave = new UserLeave;

            $user_leave->leave_type_id = $leave_type->id;
            $user_leave->leave_count = $leave[$leave_type->id];
            $user_leave->contract_id = $id;
            $user_leave->save();
        }
        $this->logActivity(['module' => 'leave','activity' => 'activity_updated','secondary_id' => $contract->User->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.leave').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.employee').' '.trans('messages.leave').' '.trans('messages.updated'));
    }

    public function destroy($id,Request $request){
        $contract = \App\Contract::find($id);

        if(!$contract){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.invalid_link'));
        }

        if(!$this->employeeAccessible($contract->User)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
        }

        $leave_uses = \App\Leave::whereUserId($contract->user_id)
            ->whereStatus('approved')
            ->where('from_date','>=',$contract->from_date)
            ->where('to_date','<=',$contract->to_date)
            ->get();

        $leave_types = \App\LeaveType::all();
        $used = [];
        foreach($leave_types as $leave_type)
           $used[$leave_type->id] = 0;

        foreach($leave_uses as $leave_use)
            $used[$leave_use->leave_type_id] = (strtotime($leave_use->to_date) - strtotime($leave_use->from_date)) / (60*60*24) + 1;

        foreach($leave_types as $leave_type)
            if($used[$leave_type->id] > 0){
                if($request->has('ajax_submit')){
                    $response = ['message' => trans('messages.employee_already_used_leave'), 'status' => 'error']; 
                    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
                }
                return redirect()->back()->withErrors(trans('messages.employee_already_used_leave'));
            }

        $this->logActivity(['module' => 'leave','activity' => 'activity_deleted','secondary_id' => $contract->User->id]);
        UserLeave::whereContractId($id)->delete();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.leave').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.employee').' '.trans('messages.leave').' '.trans('messages.deleted'));
    }
}
?>