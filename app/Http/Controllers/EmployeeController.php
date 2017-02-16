<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\EmployeeProfileRequest;
use App\Classes\Helper;
use App\User;
use App\Template;
use Entrust;
use Auth;
use App\LeaveType;
use App\SalaryType;
use App\Salary;
use App\DocumentType;
use Image;
use File;
use Mail;
use DB;
use Validator;

class EmployeeController extends Controller{
    use BasicController;
    
    protected $form = 'employee-form';

    public function index(User $employee){

        if(!Entrust::can('list_employee'))
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        if(Entrust::can('manage_all_employee'))
            $designations = \App\Designation::all()->pluck('full_designation','id')->all();
        else{
            $childs = Helper::childDesignation(Auth::user()->designation_id);
            $designations = \App\Designation::whereIn('id',$childs)->get()->pluck('full_designation','id')->all();
        }

        $roles = \App\Role::whereIsHidden(0)->get()->pluck('name','id')->all();

        $col_heads = array(
                trans('messages.option'),
                trans('messages.employee_code'),
                trans('messages.first_name'),
                trans('messages.last_name'),
                trans('messages.username'),
                trans('messages.email'),
                trans('messages.role'),
                trans('messages.designation'),
                trans('messages.status'));
        $table_info = array(
            'source' => 'employee',
            'title' => 'Employee List',
            'id' => 'employee_table'
        );

        $designation_users = User::select('designation_id', DB::raw('count(*) as total'))
             ->groupBy('designation_id')
             ->get();
        $designation_user_stat[] = array('Designation','Count');
        foreach($designation_users as $designation_user){
            $designation_user_stat[] = array($designation_user->Designation->name, $designation_user->total);
        }

        $status_users = User::select('status', DB::raw('count(*) as total'))
             ->groupBy('status')
             ->get();
        $status_user_stat[] = array('User Status','Count');
        foreach($status_users as $status_user){
            $status_user_stat[] = array(Helper::toWord($status_user->status),$status_user->total);
        }

        $departments = \App\Department::all();
        $department_user_stat[] = array('Department','Count');
        foreach($departments as $department){
            foreach($department->Designation as $designation)
            $department_user_stat[] = array($department->name,$designation->hasMany('\App\User')->count());
        }

        $role_users = DB::table('role_user')->join('roles','roles.id','=','role_user.role_id')->select('name', DB::raw('count(*) as total'))
             ->groupBy('role_id')
             ->get();
        $role_user_stat[] = array('Role','Count');
        foreach($role_users as $role_user)
            $role_user_stat[] = array(Helper::toWord($role_user->name),$role_user->total);

        $employee_graph_data = array('designation_wise_user_graph' => $designation_user_stat,'status_wise_user_graph' => $status_user_stat,'department_wise_user_graph' => $department_user_stat, 'role_wise_user_graph' => $role_user_stat);

        $assets = ['graph'];
        return view('employee.index',compact('col_heads','table_info','designations','roles','assets','employee_graph_data'));
    }

    public function lists(Request $request){

        if(Entrust::can('manage_all_employee'))
          $employees = User::all();
        elseif(Entrust::can('manage_subordinate_employee')){
          $childs = Helper::childDesignation(Auth::user()->designation_id,1);
          $employees = User::with('roles')->whereIn('designation_id',$childs)->get();
        } else
          $employees = [];

        $rows=array();

        foreach ($employees as $employee){

            foreach($employee->roles as $role)
              $role_name = Helper::toWord($role->name);

            $rows[] = array(
                    '<div class="btn-group btn-group-xs">'.
                    '<a href="/employee/'.$employee->id.'" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-right"></i></a> '.
                    (Entrust::can('delete_employee') ? delete_form(['employee.destroy',$employee->id],'employee',1) : '').
                    '</div>',
                    ($employee->Profile->employee_code != '') ? $employee->Profile->employee_code : trans('messages.na') ,
                    $employee->first_name,
                    $employee->last_name,
                    $employee->username.' '.(($employee->is_hidden) ? '<span class="label label-danger">'.trans('messages.default').'</span>' : ''),
                    $employee->email,
                    $role_name,
                    $employee->Designation->full_designation,
                    ($employee->status == 'active') ? '<span class="label label-success">'.trans('messages.active').'</span>' : '<span class="label label-danger">'.trans('messages.in_active').'</span>'
                    );  
            }
        $list['aaData'] = $rows;
        return json_encode($list);
    }

    public function profile($id = null){

        $id = ($id != null) ? $id : Auth::user()->id;
        $user = (User::find($id)) ? : Auth::user();

        if(Entrust::can('manage_all_employee')){}
        elseif(Entrust::can('manage_subordinate_employee')){
            $child_designations = Helper::childDesignation(Auth::user()->designation_id,1);
            $child_users = User::whereIn('designation_id',$child_designations)->pluck('id')->all();
            array_push($child_users, Auth::user()->id);
            if(!in_array($user->id,$child_users))
                return redirect('/profile')->withErrors(trans('messages.permission_denied'));
        } elseif($user->id != Auth::user()->id)
                return redirect('/profile')->withErrors(trans('messages.permission_denied'));

        $contract = Helper::getContract($user->id);
        $menu = ['employee'];

        return view('employee.profile',compact('user','contract','menu'));
    }

    public function show(User $employee){

        if(!$this->employeeAccessible($employee) && $employee->id != Auth::user()->id)
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        if(Entrust::can('manage_all_employee'))
            $designations = \App\Designation::all()->pluck('full_designation','id')->all();
        elseif(Entrust::can('manage_subordinate_employee')) {
            $childs = Helper::childDesignation(Auth::user()->designation_id);
            $designations = \App\Designation::whereIn('id',$childs)->get()->pluck('full_designation','id')->all();
        } else
            $designations = [];

        foreach($employee->roles as $role)
            $role_id = $role->id;

        $roles = \App\Role::whereIsHidden(0)->get()->pluck('name','id')->all();

        $gender = Helper::translateList(config('lists.gender'));
        $marital_status = Helper::translateList(config('lists.marital_status'));
        $employee_relation = Helper::translateList(config('lists.employee_relation'));
        $custom_field_values = Helper::getCustomFieldValues($this->form,$employee->id);
        $social_custom_field_values = Helper::getCustomFieldValues('employee-social-form-form',$employee->id);
        $contract_types = \App\ContractType::pluck('name','id')->all();
        $earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
        $deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();
        $leave_types = LeaveType::all();
        $contract_lists = \App\Contract::whereUserId($employee->id)->get()->pluck('full_contract_detail','id')->all();
        $office_shifts = \App\OfficeShift::all()->pluck('name','id')->all();
        $document_types = DocumentType::pluck('name','id')->all();

        $templates = \App\Template::whereIsDefault(0)->pluck('name','id')->all();

        $assets = ['rte'];
        $menu = ['employee'];

        return view('employee.show',compact('employee','designations','assets','menu','role','roles','gender','marital_status','custom_field_values','employee_relation','social_custom_field_values','contract_types','earning_salary_types','deduction_salary_types','leave_types','contract_lists','office_shifts','document_types','templates'));
    }

    public function edit(User $employee){
      $child_designations = Helper::childDesignation(Auth::user()->designation_id,1);

      if(!Entrust::can('edit_employee') || !$this->employeeAccessible($employee))
          return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

      foreach($employee->roles as $role)
        $role_id = $role->id;

      $query = \App\Designation::whereNotNull('id');

      if(!Entrust::can('manage_all_employee'))
        $query->whereIn('id',$child_designations);

      $designations = $query->get()->pluck('full_designation','id')->all();

        if(defaultRole())
            $roles = \App\Role::pluck('name','id')->all();
        else
            $roles = \App\Role::where('name','!=','admin')->pluck('name','id')->all();

      $custom_field_values = Helper::getCustomFieldValues($this->form,$employee->id);
        $menu = ['employee'];

      return view('employee.edit',compact('employee','designations','roles','role_id','custom_field_values','menu'));
    }

    public function profileUpdate(EmployeeProfileRequest $request, $id){
        $employee = User::find($id);

        if(!$employee){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('employee')->withErrors(trans('messages.invalid_link'));
        }

        if(!$this->employeeAccessible($employee)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
        }

        if($request->input('type') == 'social_networking'){
            $validation = Helper::validateCustomField('employee-social-form',$request);
            
            if($validation->fails()){
                if($request->has('ajax_submit')){
                    $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
                }
                return redirect()->back()->withInput()->withErrors($validation->messages());
            }
        }

        $profile = $employee->Profile ?: new Profile;
        $employee->profile()->save($profile);
        $photo = $profile->photo;
        $data = $request->all();
        $profile->fill($data);

        if ($request->hasFile('photo') && $request->input('remove_photo') != 1) {
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filename = uniqid();
            $file = $request->file('photo')->move(config('constants.upload_path.profile_image'), $filename.".".$extension);
            $img = Image::make(config('constants.upload_path.profile_image').$filename.".".$extension);
            $img->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save(config('constants.upload_path.profile_image').$filename.".".$extension);
            $profile->photo = $filename.".".$extension;
        } elseif($request->input('remove_photo') == 1){
            File::delete(config('constants.upload_path.profile_image').$profile->photo);
            $profile->photo = null;
        }
        else
        $profile->photo = $photo;

        if($request->input('type') == 'social_networking')
            Helper::updateCustomField('employee-social-form',$employee->id, $data);

        $profile->save();

        $this->logActivity(['module' => 'employee','unique_id' => $employee->id,'activity' => 'activity_profile_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.profile').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }

        return redirect('/employee/'.$id.'/#'.$request->input('type'))->withSuccess(trans('messages.employee').' '.trans('messages.profile').' '.trans('messages.updated'));
    }

    public function update(EmployeeRequest $request, User $employee){
       
        $validation = Helper::validateCustomField($this->form,$request);
        
        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withInput()->withErrors($validation->messages());
        }

        if(!Entrust::can('edit_employee') || !$this->employeeAccessible($employee)){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
        }

        $employee->first_name = $request->input('first_name');
        $employee->last_name = $request->input('last_name');
        $employee->email = $request->input('email');
        if($request->has('designation_id'))
        $employee->designation_id = $request->input('designation_id');

        if(defaultRole() && $request->has('role_id')){
          $roles[] = $request->input('role_id');
          $employee->roles()->sync($roles);
        }
        $employee->save();

        $profile = $employee->Profile ?: new Profile;
        $profile->gender = $request->input('gender');
        $profile->marital_status = $request->input('marital_status');
        $profile->employee_code = $request->input('employee_code');
        $profile->date_of_birth = ($request->input('date_of_birth')) ? : null;
        $profile->date_of_joining = ($request->input('date_of_joining')) ? : null;
        $profile->date_of_leaving = ($request->input('date_of_leaving')) ? : null;
        $employee->profile()->save($profile);

        if(isset($profile->date_of_leaving) && $profile->date_of_leaving < date('Y-m-d'))
            $employee->status = 'in-active';
        else
            $employee->status = 'active';
        $employee->save();

        Helper::updateCustomField($this->form,$employee->id, $request->all());

        $this->logActivity(['module' => 'employee','unique_id' => $employee->id,'activity' => 'activity_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee/'.$employee->id.'#basic')->withSuccess(trans('messages.employee').' '.trans('messages.updated'));
    }

    public function accountInvalid(){

      if(Auth::user()->Profile->date_of_leaving > date('Y-m-d'))
        return redirect('/dashboard');

      return view('employee.account_invalid');
    }

    public function changePassword(){
      return view('auth.change_password');
    }


    public function doChangePassword(Request $request){
        if(!getMode()){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.disable_message'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.disable_message'));
        }

        $credentials = $request->only(
                'new_password', 'new_password_confirmation'
        );

        $validation = Validator::make($request->all(),[
            'old_password' => 'required|valid_password',
            'new_password' => 'required|confirmed|different:old_password|min:6',
            'new_password_confirmation' => 'required|different:old_password|same:new_password'
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors($validation->messages()->first());
        }

        $user = Auth::user();
        
        $user->password = bcrypt($credentials['new_password']);
        $user->save();
        $this->logActivity(['module' => 'authentication','activity' => 'activity_password_changed']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.password_changed'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        
        return redirect()->back()->withErrors(trans('messages.password_changed'));
    }

    public function doChangeEmployeePassword(Request $request, $id){
        if(!getMode()){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.disable_message'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.disable_message'));
        }
        $employee = User::find($id);
        

        $validation = Validator::make($request->all(),[
            'new_password' => 'required|confirmed|min:6',
            'new_password_confirmation' => 'required|same:new_password'
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors($validation->messages()->first());
        }

        $credentials = $request->only(
                'new_password', 'new_password_confirmation'
        );

        $employee->password = bcrypt($credentials['new_password']);
        $employee->save();
        $this->logActivity(['module' => 'authentication','activity' => 'activity_employee_password_changed']);

        $response = ['message' => trans('messages.password_changed'), 'status' => 'success']; 
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        
        return redirect()->back()->withSuccess(trans('messages.password_changed'));    
    }

    public function email(Request $request, $id){
        $validation = Validator::make($request->all(),[
            'subject' => 'required',
            'body' => 'required'
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors($validation->messages()->first());
        }

        $user = User::find($id);
        $mail['email'] = $user->email;
        $mail['subject'] = $request->input('subject');
        $body = $request->input('body');

        \Mail::send('emails.email', compact('body'), function($message) use ($mail){
            $message->to($mail['email'])->subject($mail['subject']);
        });
        $this->logEmail(array('to' => $mail['email'],'subject' => $mail['subject'],'body' => $body));

        $this->logActivity(['module' => 'employee','unique_id' => $user->id,'activity' => 'mail_sent']);
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.mail').' '.trans('messages.sent'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.mail').' '.trans('messages.sent'));
    }

    public function destroy(User $employee,Request $request){

        if(!Entrust::can('delete_employee') || !$this->employeeAccessible($employee) || $employee->is_hidden){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
          return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));
        }

        if($employee->id == Auth::user()->id){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/employee')->withErrors(trans('message.unable_to_delete_yourself'));
        }

        Helper::deleteCustomField($this->form, $employee->id);
        $this->logActivity(['module' => 'employee','unique_id' => $employee->id,'activity' => 'activity_deleted']);

        $employee->delete();
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.deleted'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee')->withSuccess(trans('messages.employee').' '.trans('messages.deleted'));
    }
}