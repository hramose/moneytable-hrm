<?php
namespace App\Http\Controllers;
use App\Classes\Helper;
use App\UserLocation;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\UserLocationRequest;

Class UserLocationController extends Controller{
    use BasicController;

    public function lists(Request $request){
        $data = '';

        $employee = \App\User::find($request->input('employee_id'));

        if(!$employee)
            return $data;

        $user_locations = $employee->UserLocation->sortBy('from_date');
        foreach($user_locations as $key => $user_location){
        $data .= '<tr>
                <td>'.showDate($user_location->from_date).(($user_location->to_date) ? (' to '.showDate($user_location->to_date)) : '').'</td>
                <td>'.$user_location->Location->name.'</td>
                <td>';
                $data .= '<div class="btn-group btn-group-xs">
                        <a href="#" data-href="/user-location/'.$user_location->id.'/edit" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit" data-toggle="tooltip" title="'.trans('messages.edit').'"></i></a>'.
                            delete_form(['user-location.destroy',$user_location->id]).
                    '</div>';
               $data .= '</td>
            </tr>';
        }

        return $data;
    }

    public function store(UserLocationRequest $request, $id){
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

        if(UserLocation::whereUserId($id)->whereNull('to_date')->count()){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.already_undefined_end_date'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee/'.$id.'#location')->withErrors(trans('messages.already_undefined_end_date'));
        }

        $previous_location = UserLocation::whereUserId($id)->first();

        if($previous_location && $request->input('from_date') <= $previous_location->from_date){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.back_date_entry'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee/'.$id.'#location')->withErrors(trans('messages.back_date_entry'));
        }

        if($request->has('to_date'))
            $location = UserLocation::whereUserId($id)
                ->where(function ($query) use($request) { $query->where(function ($query) use($request){
                    $query->where('from_date','<=',$request->input('from_date'))
                    ->where('to_date','>=',$request->input('from_date'));
                    })->orWhere(function ($query) use($request) {
                        $query->where('from_date','<=',$request->input('to_date'))
                            ->where('to_date','>=',$request->input('to_date'));
                    });})->count();
        else
            $location = UserLocation::whereUserId($id)->where('from_date','<=',$request->input('from_date'))
                        ->where('to_date','>=',$request->input('from_date'))->count();

        if($location){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.location_already_defined'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee/'.$id.'#location')->withErrors(trans('messages.location_already_defined'));
        }

        $user_location = new UserLocation;
        $data = $request->all();
        $data['user_id'] = $id;
        $data['to_date'] = ($request->has('to_date')) ? $request->input('to_date') : null;
        $user_location->fill($data)->save();
        $this->logActivity(['module' => 'user_location','activity' => 'activity_added','secondary_id' => $employee->id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.location').' '.trans('messages.added'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$id.'#location')->withSuccess(trans('messages.location').' '.trans('messages.added'));
    }

    public function edit(UserLocation $user_location){
        $employee = \App\User::find($user_location->user_id);
        $locations = \App\Location::all()->pluck('name','id')->all();

        if(!$this->employeeAccessible($employee))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        return view('employee.edit_user_location',compact('user_location','locations','employee'));
    }

    public function update(UserLocationRequest $request, UserLocation $user_location){

        if(UserLocation::whereUserId($user_location->user_id)->where('id','!=',$user_location->id)->whereNull('to_date')->count()){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.already_undefined_end_date'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee/'.$user_location->user_id.'#location')->withErrors(trans('messages.already_undefined_end_date'));
        }

        $previous_location = UserLocation::whereUserId($user_location->user_id)->where('id','!=',$user_location->id)->first();

        if($previous_location && $request->input('from_date') <= $previous_location->from_date){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.back_date_entry'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee/'.$user_location->user_id.'#location')->withErrors(trans('messages.back_date_entry'));
        }

        if($request->has('to_date'))
            $location = UserLocation::whereUserId($user_location->user_id)->where('id','!=',$user_location->id)
                ->where(function ($query) use($request) { $query->where(function ($query) use($request){
                    $query->where('from_date','<=',$request->input('from_date'))
                    ->where('to_date','>=',$request->input('from_date'));
                    })->orWhere(function ($query) use($request) {
                        $query->where('from_date','<=',$request->input('to_date'))
                            ->where('to_date','>=',$request->input('to_date'));
                    });})->count();
        else
            $location = UserLocation::whereUserId($user_location->user_id)->where('id','!=',$user_location->id)->where('from_date','<=',$request->input('from_date'))
                        ->where('to_date','>=',$request->input('from_date'))->count();

        if($location){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.entry_already_defined'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee/'.$user_location->user_id.'#location')->withErrors(trans('messages.entry_already_defined'));
        }

        $data = $request->all();
        $data['to_date'] = ($request->has('to_date')) ? $request->input('to_date') : null;
        $user_location->fill($data)->save();
        $this->logActivity(['module' => 'user_location','activity' => 'activity_updated','secondary_id' => $user_location->user_id]);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.location').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$user_location->user_id.'#location')->withSuccess(trans('messages.location').' '.trans('messages.updated'));
    }

    public function destroy(UserLocation $user_location,Request $request){
        if(!$this->employeeAccessible($user_location->User)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
        }

        $this->logActivity(['module' => 'user_location','activity' => 'activity_deleted','secondary_id' => $user_location->user_id]);
        $user_location->delete();
        
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.location').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.location').' '.trans('messages.deleted'));
    }
}
?>