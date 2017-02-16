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
                trans('messages.location'),
                trans('messages.status'));
        $table_info = array(
            'source' => 'employee',
            'title' => 'Employee List',
            'id' => 'employee_table',
            'form' => 'employee_filter_form'
        );

        $designation_users = User::select('designation_id', DB::raw('count(*) as total'))
             ->groupBy('designation_id')
             ->get();
        $designation_user_stat[] = array('Designation','Count');
        foreach($designation_users as $designation_user){
            $designation_user_stat[] = array($designation_user->Designation->name, $designation_user->total);
        }

        $location_data = array();
        foreach(\App\Location::all() as $location)
            $location_data[$location->name] = 0;
        foreach(User::whereStatus('active')->get() as $user){
            $user_location = Helper::getLocation(date('Y-m-d'),$user->id);
            if(array_key_exists($user_location,$location_data))
                $location_data[$user_location]++;
        }

        $location_stat[] = array('Location','Count');
        foreach($location_data as $key => $value)
            $location_stat[] = array($key,$value);

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
            $department_user_count = 0;
            foreach($department->Designation as $designation)
                $department_user_count += $designation->hasMany('\App\User')->count();
            $department_user_stat[] = array($department->name,$department_user_count);
        }
        $locations = \App\Location::all()->pluck('name','id')->all();

        $role_users = DB::table('role_user')->join('roles','roles.id','=','role_user.role_id')->select('name', DB::raw('count(*) as total'))
             ->groupBy('role_id')
             ->get();
        $role_user_stat[] = array('Role','Count');
        foreach($role_users as $role_user)
            $role_user_stat[] = array(Helper::toWord($role_user->name),$role_user->total);

        $employee_graph_data = array('designation_wise_user_graph' => $designation_user_stat,'status_wise_user_graph' => $status_user_stat,'department_wise_user_graph' => $department_user_stat, 'role_wise_user_graph' => $role_user_stat,'location_wise_user_graph' => $location_stat);

        $assets = ['graph'];
        return view('employee.index',compact('col_heads','table_info','designations','roles','assets','employee_graph_data','locations'));
    }

    public function lists(Request $request){

        if(defaultRole())
          $query = User::whereNotNull('id');
        elseif(Entrust::can('manage_all_employee'))
          $query = User::whereIsHidden(0);
        elseif(Entrust::can('manage_subordinate_employee')){
          $childs = Helper::childDesignation(Auth::user()->designation_id,1);
          $query = User::whereIn('designation_id',$childs);
        } else
          $query = User::whereNull('id');

        if($request->has('role_id'))
            $query->whereHas('roles',function($q) use ($request){
                $q->where('role_id','=',$request->input('role_id'));
            });

        $employees = $query->get();

        $rows=array();
        $location = ($request->has('location_id')) ? \App\Location::whereId($request->input('location_id'))->first() : null;
        
        if($request->input('designation_id'))
            $employees = $employees->whereLoose('designation_id',$request->input('designation_id'))->all();

        if($request->input('status'))
            $employees = $employees->whereLoose('status',$request->input('status'))->all();

        foreach ($employees as $employee){

            foreach($employee->roles as $role)
              $role_name = Helper::toWord($role->name);

            if(!$location || $location->name == Helper::getLocation(date('Y-m-d'),$employee->id))
            $rows[] = array(
                    '<div class="btn-group btn-group-xs">'.
                    '<a href="/employee/'.$employee->id.'" class="btn btn-default btn-xs" data-toggle="tooltip" title="'.trans('messages.view').'"> <i class="fa fa-arrow-circle-right"></i></a> '.
                    (($employee->status != 'in-active') ? '<a href="#" data-href="/employee/'.$employee->id.'/change-status" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal"> <i class="fa fa-user" data-toggle="tooltip" title="'.trans('messages.change').' '.trans('messages.status').'"></i></a> ' : '').
                    (Entrust::can('delete_employee') ? delete_form(['employee.destroy',$employee->id],'employee',1) : '').
                    '</div>',
                    ($employee->Profile->employee_code != '') ? $employee->Profile->employee_code : trans('messages.na') ,
                    $employee->first_name,
                    $employee->last_name,
                    $employee->username.' '.(($employee->is_hidden) ? '<span class="label label-danger">'.trans('messages.default').'</span>' : ''),
                    $employee->email,
                    $role_name,
                    $employee->Designation->full_designation,
                    Helper::getLocation(date('Y-m-d'),$employee->id),
                    ($employee->status == 'active' || $employee->status == '') ? '<span class="label label-success">'.trans('messages.active').'</span>' : '<span class="label label-danger">'.Helper::toWord($employee->status).'</span>'
                    );  
            }
        $list['aaData'] = $rows;
        return json_encode($list);
    }

    public function register(){

        if(!config('config.enable_registration'))
            return redirect('/');

        $locations = \App\Location::all()->pluck('name','id')->all();
        return view('auth.register',compact('locations'));
    }

    public function postRegister(Request $request){

        if(!config('config.enable_registration'))
            return redirect('/');

        $validation = Validator::make($request->all(),[
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required|confirmed|min:6',
            'email' => 'required|email|max:255|unique:users',
            'username' => 'required|min:4|max:255|unique:users',
            'password_confirmation' => 'required|same:password'
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors($validation->messages()->first());
        }

        if(!preg_match('/^[a-zA-Z0-9_\.\-]*$/',$request->input('username'))){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.username_allowed_characters'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.username_allowed_characters'));
        }

        $default_designation = \App\Designation::whereIsDefault(1)->first();
        $default_role = \App\Role::whereIsDefault(1)->first();

        if(!$default_designation || !$default_role){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.permission_denied'));
        }

        $user = new \App\User;
        $user->fill($request->all());
        $user->password = bcrypt($request->input('password'));
        $user->designation_id = $default_designation->id;
        $user->status = 'pending_activation';
        $user->activation_token = randomString('30','token');
        $user->save();
        $profile = new \App\Profile;
        $profile->user()->associate($user);
        $profile->date_of_joining = date('Y-m-d');
        $profile->save();
        $user->attachRole($default_role->id);

        $user_location = new \App\UserLocation;
        $user_location->from_date = date('Y-m-d');
        $user_location->location_id = $request->input('location_id');
        $user_location->user_id = $user->id;
        $user_location->save();

        \Mail::send('emails.default.account_activation', compact('user'), function($message) use ($user){
            $message->to($user->email)->subject('User Activation');
        });

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.registration_complete').' '.trans('messages.activate_your_account'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.registration_complete').' '.trans('messages.activate_your_account'));  
    }

    public function resendActivation(){

        if(!config('config.enable_registration'))
            return redirect('/');

        return view('auth.resend_activation');
    }

    public function postResendActivation(Request $request){

        if(!config('config.enable_registration'))
            return redirect('/');

        $user = \App\User::whereEmail($request->input('email'))->first();

        if(!$user){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.no_user_with_email'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.no_user_with_email'));
        } elseif($user->status != 'pending_activation'){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.invalid_link'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors(trans('messages.invalid_link'));
        }

        \Mail::send('emails.default.account_activation', compact('user'), function($message) use ($user){
            $message->to($user->email)->subject('User Activation');
        });

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.activation_email_sent'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/')->withSuccess(trans('messages.activation_email_sent'));
    }

    public function activateAccount($token = null){

        if(!config('config.enable_registration'))
            return redirect('/');
        
        if($token == null)
            return redirect('/');

        $user = \App\User::whereActivationToken($token)->first();

        if(!$user)
            return redirect('/')->withErrors(trans('messages.invalid_link'));

        if($user->status != 'pending_activation')
            return redirect('/')->withErrors(trans('messages.invalid_link'));

        $user->status = 'pending_approval';
        $user->save();
        return redirect('/')->withSuccess(trans('messages.account_activated'));
    }

    public function changeStatus($id){

        $employee = \App\User::find($id);

        if(!$employee || !$this->employeeAccessible($employee) || $employee->hasRole(DEFAULT_ROLE) || $employee->status == 'in-active')
            return view('common.error',['message' => trans('messages.permission_denied')]);

        return view('employee.change_status',compact('employee'));

    }

    public function postChangeStatus(Request $request, $id){

        $employee = \App\User::find($id);

        if(!$employee || !$this->employeeAccessible($employee))
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        $validation = Validator::make($request->all(),[
            'status' => 'required'
        ]);

        if($validation->fails()){
            if($request->has('ajax_submit')){
                $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect()->back()->withErrors($validation->messages()->first());
        }

        $employee->status = $request->input('status');
        $employee->save();

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.status').' '.trans('messages.updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect('/employee')->withSuccess(trans('messages.employee').' '.trans('messages.status').' '.trans('messages.updated'));
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

        $education_levels = \App\EducationLevel::pluck('name','id')->all();
        $qualification_languages = \App\QualificationLanguage::pluck('name','id')->all();
        $qualification_skills = \App\QualificationSkill::pluck('name','id')->all();
        $employee_relation = Helper::translateList(config('lists.employee_relation'));
        $document_types = DocumentType::pluck('name','id')->all();

        $menu = ['employee'];

        return view('employee.profile',compact('user','contract','menu','education_levels','qualification_languages','qualification_skills','employee_relation','document_types'));
    }

    public function show(User $employee){

        if(!$this->employeeAccessible($employee) && $employee->id != Auth::user()->id)
            return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

        if(!defaultRole() && $employee->hasRole(DEFAULT_ROLE))
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
        $education_levels = \App\EducationLevel::pluck('name','id')->all();
        $qualification_languages = \App\QualificationLanguage::pluck('name','id')->all();
        $qualification_skills = \App\QualificationSkill::pluck('name','id')->all();
        $earning_salary_types = SalaryType::where('salary_type','=','earning')->get();
        $deduction_salary_types = SalaryType::where('salary_type','=','deduction')->get();
        $leave_types = LeaveType::all();
        $contract_lists = \App\Contract::whereUserId($employee->id)->get()->pluck('full_contract_detail','id')->all();
        $office_shifts = \App\OfficeShift::all()->pluck('name','id')->all();
        $locations = \App\Location::all()->pluck('name','id')->all();
        $document_types = DocumentType::pluck('name','id')->all();

        $templates = \App\Template::whereIsDefault(0)->pluck('name','id')->all();

        $assets = ['rte'];
        $menu = ['employee'];

        return view('employee.show',compact('employee','designations','assets','menu','role','roles','gender','marital_status','custom_field_values','employee_relation','social_custom_field_values','contract_types','earning_salary_types','deduction_salary_types','leave_types','contract_lists','office_shifts','document_types','templates','education_levels','qualification_languages','qualification_skills','locations'));
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

        $this->logActivity(['module' => 'profile','unique_id' => $employee->id,'activity' => 'activity_updated']);

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
        
        if(!preg_match('/^[a-zA-Z0-9_\.\-]*$/',$request->input('username'))){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.username_allowed_characters'), 'status' => 'error']; 
                return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
            }
            return redirect('/dashboard')->withErrors(trans('messages.username_allowed_characters'));
        }

        $employee->first_name = $request->input('first_name');
        $employee->last_name = $request->input('last_name');
        $employee->username = $request->input('username');
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
        $profile->contact_number = $request->input('contact_number');
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
        $this->logActivity(['module' => 'authentication','activity' => 'activity_password_changed','secondary_id' => $employee->id]);

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

        $this->logActivity(['module' => 'employee','unique_id' => $user->id,'activity' => 'activity_mail_sent']);
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