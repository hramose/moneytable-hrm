<?php
namespace App\Http\Controllers;
use App\Classes\Helper;
use App\UserShift;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\UserShiftRequest;

Class UserShiftController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $data = '';

        $employee = \App\User::find($request->input('employee_id'));

        if(!$employee)
            return $data;

        foreach($employee->UserShift as $user_shift){
        $data .= '<tr>
                <td>'.showDate($user_shift->from_date).' to '.showDate($user_shift->to_date).'</td>
                <td>'.$user_shift->OfficeShift->name.'</td>
                <td>
                    <div class="btn-group btn-group-xs">
                        <a href="#" data-href="/user-shift/'.$user_shift->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
                            delete_form(['user-shift.destroy',$user_shift->id]).
                    '</div>
                </td>
            </tr>';
        }

        return $data;
    }

    public function store(UserShiftRequest $request, $id){
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

        $shift = UserShift::whereUserId($id)
            ->where(function ($query) use($request) { $query->where(function ($query) use($request){
                $query->where('from_date','<=',$request->input('from_date'))
                ->where('to_date','>=',$request->input('from_date'));
                })->orWhere(function ($query) use($request) {
                    $query->where('from_date','<=',$request->input('to_date'))
                        ->where('to_date','>=',$request->input('to_date'));
                });})->count();

        if($shift){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.shift_already_defined'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee/'.$id.'#office_shift')->withErrors(trans('messages.shift_already_defined'));
        }

        $user_shift = new UserShift;
        $data = $request->all();
        $data['user_id'] = $id;
        $user_shift->fill($data)->save();
        $this->logActivity(['module' => 'office_shift','activity' => 'activity_added','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.shift').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$id.'#office_shift')->withSuccess(trans('messages.shift').' '.trans('messages.added'));
    }

    public function edit(UserShift $user_shift){
        $employee = \App\User::find($user_shift->user_id);
        $shifts = \App\OfficeShift::all();

        if(!$this->employeeAccessible($employee))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        foreach($shifts as $shift)
            $office_shifts[$shift->id] = $shift->name;

        return view('employee.edit_user_shift',compact('user_shift','office_shifts','employee'));
    }

    public function update(UserShiftRequest $request, UserShift $user_shift){

        $shift = UserShift::whereUserId($user_shift->user_id)
            ->where('id','!=',$user_shift->id)
            ->where(function ($query) use($request) { $query->where(function ($query) use($request){
                $query->where('from_date','<=',$request->input('from_date'))
                ->where('to_date','>=',$request->input('from_date'));
                })->orWhere(function ($query) use($request) {
                    $query->where('from_date','<=',$request->input('to_date'))
                        ->where('to_date','>=',$request->input('to_date'));
                });})->count();

        if($shift){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.shift_already_defined'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee/'.$user_shift->user_id.'#office_shift')->withErrors(trans('messages.shift_already_defined'));
        }

        $user_shift->fill($request->all())->save();
        $this->logActivity(['module' => 'office_shift','activity' => 'activity_updated','secondary_id' => $user_shift->user_id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.shift').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$user_shift->user_id.'#office_shift')->withSuccess(trans('messages.shift').' '.trans('messages.updated'));
    }

    public function destroy(UserShift $user_shift,Request $request){
        if(!$this->employeeAccessible($user_shift->User)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
        }

        $this->logActivity(['module' => 'office_shift','activity' => 'activity_deleted','secondary_id' => $user_shift->user_id]);
        $user_shift->delete();
        
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.shift').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.shift').' '.trans('messages.deleted'));
    }
}
?>