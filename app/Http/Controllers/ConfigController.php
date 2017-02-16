<?php
namespace App\Http\Controllers;
use DB;
use File;
use Auth;
use Image;
use Entrust;
use App\Classes\Helper;
use Illuminate\Http\Request;
use App\Http\Requests\ConfigurationTimeRequest;
use Validator;
use Swift_SmtpTransport;
use Swift_TransportException;
use Services_Twilio;
use Services_Twilio_RestException;

Class ConfigController extends Controller{
    use BasicController;

	public function index(){
        $assets = ['timepicker','mail_config'];
        $languages = array();
        foreach(config('lang') as $key => $value)
        	$languages[$key] = $value['language'];

        if(\App\Setup::whereModule('configuration')->whereCompleted(0)->first())
        	\App\Setup::whereModule('configuration')->whereCompleted(0)->update(['completed' => 1]);

		return view('configuration.index',compact('assets','languages'));
	}

	public function permission(){

        $permissions = DB::table('permissions')->orderBy('category')->get();
        
        $permission_role = DB::table('permission_role')
        	->select(DB::raw('CONCAT(role_id,"-",permission_id) AS detail,id'))
        	->pluck('detail','id');
        $category = null;

		return view('configuration.permission',compact('permissions','permission_role','category'));
	}

	public function api(Request $request){
		$user = Auth::user();
		$user->auth_token = str_random(40);
		$user->save();

		$this->logActivity(['module' => 'configuration','activity' => 'activity_api_token_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.api_token_updated'), 'status' => 'success','auth_token' => $user->auth_token]; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#'.$request->input('config_type'))->withSuccess(trans('messages.api_token_updated'));	
	}	

	public function mailStore(Request $request){

		$validation = Validator::make($request->all(),[
				'from_address' => 'required|email',
				'from_name' => 'required',
				'host' => 'required_if:driver,smtp',
				'port' => 'required_if:driver,smtp|numeric',
				'username' => 'required_if:driver,smtp',
				'password' => 'required_if:driver,smtp',
				'encryption' => 'in:ssl,tls|required_if:driver,smtp',
				'mailgun_host' => 'required_if:driver,mailgun',
				'mailgun_port' => 'required_if:driver,mailgun|numeric',
				'mailgun_username' => 'required_if:driver,mailgun',
				'mailgun_password' => 'required_if:driver,mailgun',
				'mailgun_domain' => 'required_if:driver,mailgun',
				'mailgun_secret' => 'required_if:driver,mailgun',
				'mandrill_secret' => 'required_if:driver,mandrill',
				]);

		if($validation->fails()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withInput()->withErrors($validation->messages());
		}

		if($request->input('driver') == 'smtp'){
			$stmp = 0;
			try{
			        $transport = Swift_SmtpTransport::newInstance($request->input('host'), $request->input('port'), $request->input('encryption'));
			        $transport->setUsername($request->input('username'));
			        $transport->setPassword($request->input('password'));
			        $mailer = \Swift_Mailer::newInstance($transport);
			        $mailer->getTransport()->start();
			        $stmp =  1;
			    } catch (Swift_TransportException $e) {
			        $stmp =  $e->getMessage();
			    } 

			if($stmp != 1){
		        if($request->has('ajax_submit')){
		            $response = ['message' => $stmp, 'status' => 'error']; 
		            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		        }
				return redirect()->back()->withInput()->withErrors($stmp);
			}
		}
		
		$input = $request->all();
		foreach($input as $key => $value){
        	if(!in_array($key, config('constants.ignore_var'))){
        		$config = \App\Config::firstOrNew(['name' => $key]);
        		$config->value = $value;
        		$config->save();
        	}
        }

        $config_type = $request->input('config_type');

		if($config_type == 'mail' && \App\Setup::whereModule('mail')->whereCompleted(0)->first())
        	\App\Setup::whereModule('mail')->whereCompleted(0)->update(['completed' => 1]);

		$this->logActivity(['module' => 'configuration','activity' => 'activity_mail_configuration_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.mail_configuration_updated'), 'status' => 'success']; 
	        if(config('config.application_setup_info') && defaultRole()){
	        	$setup_data = Helper::setupInfo();
	        	$response['setup_data'] = $setup_data;
	        }
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#'.$config_type)->withSuccess(trans('messages.mail_configuration_updated'));			

	}

	public function smsStore(Request $request){

		$validation = Validator::make($request->all(),[
				'sid' => 'required',
				'token' => 'required',
				'from' => 'required',
				]);

		if($validation->fails()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withInput()->withErrors($validation->messages());
		}

		$sms = 0;
        $client = new Services_Twilio($request->input('sid'), $request->input('token'));
        try{
          $message = $client->account->messages->create(array(
              "From" => $request->input('from'),
              "To" => '57545',
              "Body" => 'hello'
          ));
          $sms = 1;
        } catch (Services_Twilio_RestException $e) {
          $sms = $e->getMessage();
        }

		if($sms != 1){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $sms, 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect()->back()->withInput()->withErrors($sms);
		}

		$sms_config = config('twilio');

		$config_type = $request->input('config_type');

		$sms_config['sid'] = $request->input('sid');
		$sms_config['token'] = $request->input('token');
		$sms_config['from'] = $request->input('from');

		$filename = base_path().config('constants.path.sms');
		File::put($filename,var_export($sms_config, true));
		File::prepend($filename,'<?php return ');
		File::append($filename, ';');

		$this->logActivity(['module' => 'configuration','activity' => 'activity_sms_configuration_updated']);

        if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.sms_configuration_updated'), 'status' => 'success']; 
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        }
		return redirect('/configuration#'.$config_type)->withSuccess(trans('messages.sms_configuration_updated'));			

	}

	public function savePermission(Request $request){

		$input = $request->all();
		$permissions = ($request->input('permission')) ? : [];
		foreach($permissions as $key => $permission)
			foreach($permission as $k => $perm)
				$insert[] = array('permission_id' => $k,'role_id' => $key);

        $permissions = DB::table('permissions')->get();
        DB::table('permission_role')->truncate();
        foreach($permissions as $permission)
            $insert[] = array('permission_id' => $permission->id,'role_id' => 1);
        DB::table('permission_role')->insert($insert);

        if(\App\Setup::whereModule('permission')->whereCompleted(0)->first())
        	\App\Setup::whereModule('permission')->whereCompleted(0)->update(['completed' => 1]);

		$this->logActivity(['module' => 'configuration','activity' => 'activity_permission_updated']);

    	if($request->has('ajax_submit')){
            $response = ['message' => trans('messages.permission_updated'), 'status' => 'success']; 
	        if(config('config.application_setup_info') && defaultRole()){
	        	$setup_data = Helper::setupInfo();
	        	$response['setup_data'] = $setup_data;
	        }
            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
    	}
		return redirect('/permission')->withSuccess(trans('messages.permission_updated'));
	}

	public function logoStore(Request $request){

        $validation = Validator::make($request->all(),[
            'logo' => 'image'
        ]);

        if($validation->fails()){
        	if($request->has('ajax_submit')){
	            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        	}

        	return redirect()->back()->withErrors($validation->messages()->first());
        }

		$filename = uniqid();
		$config = \App\Config::firstOrNew(['name' => 'logo']);

		if ($request->hasFile('logo') && $request->input('remove_logo') != 1){
			if(File::exists(config('constants.upload_path.logo').config('config.logo')))
				File::delete(config('constants.upload_path.logo').config('config.logo'));
            $extension = $request->file('logo')->getClientOriginalExtension();
            $file = $request->file('logo')->move(config('constants.upload_path.logo'), $filename.".".$extension);
            $img = Image::make(config('constants.upload_path.logo').$filename.".".$extension);
            $img->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save(config('constants.upload_path.logo').$filename.".".$extension);
            $config->value = $filename.".".$extension;
		} elseif($request->input('remove_logo') == 1){
			if(File::exists(config('constants.upload_path.logo').config('config.logo')))
				File::delete(config('constants.upload_path.logo').config('config.logo'));
			$config->value = null;
        }

        $config->save();

		$this->logActivity(['module' => 'configuration','activity' => 'activity_configuration_updated']);

    	if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.configuration_updated'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }

		return redirect('/configuration#'.$request->input('config_type'))->withSuccess(trans('messages.configuration_updated'));	
	}

	public function store(Request $request){
		
        $validation = Validator::make($request->all(),[
            'company_name' => 'sometimes|required',
            'contact_person' => 'sometimes|required',
            'email' => 'sometimes|email|required',
            'country_id' => 'sometimes|required',
            'timezone_id' => 'sometimes|required',
            'application_name' => 'sometimes|required',
            'currency_decimal' => 'sometimes|required',
            'default_currency_symbol' => 'sometimes|required',
            'currency_decimal' => 'sometimes|required',
            'timezone_id' => 'sometimes|required',
            'currency_decimal' => 'numeric',
            'from_address' => 'sometimes|email',
            'auto_lock_attendance_days' => 'sometimes|required|min:1|numeric',
            'enable_future_attendance' => 'sometimes|required|min:0|numeric',
            'late_comer_grace_time' => 'sometimes|required|min:0|numeric',
        ]);

        if($validation->fails()){
        	if($request->has('ajax_submit')){
	            $response = ['message' => $validation->messages()->first(), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
        	}

        	return redirect()->back()->withErrors($validation->messages()->first());
        }

		$input = $request->all();
		foreach($input as $key => $value){
        	if(!in_array($key, config('constants.ignore_var'))){
        		$config = \App\Config::firstOrNew(['name' => $key]);
        		$config->value = $value;
        		$config->save();
        	}
        }

		$config_type = $request->input('config_type');

		if($config_type == 'general' && \App\Setup::whereModule('general_configuration')->whereCompleted(0)->first())
        	\App\Setup::whereModule('general_configuration')->whereCompleted(0)->update(['completed' => 1]);
        elseif($config_type == 'system' && \App\Setup::whereModule('system_configuration')->whereCompleted(0)->first())
        	\App\Setup::whereModule('system_configuration')->whereCompleted(0)->update(['completed' => 1]);

		$this->logActivity(['module' => 'configuration','activity' => 'activity_configuration_updated']);

        	if($request->has('ajax_submit')){
		        $response = ['message' => trans('messages.configuration_updated'), 'status' => 'success']; 
		        if(config('config.application_setup_info') && defaultRole()){
		        	$setup_data = Helper::setupInfo();
		        	$response['setup_data'] = $setup_data;
		        }
		        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		    }

		return redirect('/configuration#'.$config_type)->withSuccess(trans('messages.configuration_updated'));	
	}

	public function menuStore(Request $request){

		$data = $request->all();
		foreach(\App\Menu::all() as $menu_item){
			$menu_item->order = $request->input($menu_item->name);
			$menu_item->visible = $request->has($menu_item->name.'-visible') ? 1 : 0;
			$menu_item->save();
		}

		$config_type = $request->input('config_type');
		if($config_type == 'menu' && \App\Setup::whereModule('menu')->whereCompleted(0)->first())
        	\App\Setup::whereModule('menu')->whereCompleted(0)->update(['completed' => 1]);
        
		$response = ['status' => 'success','message' => trans('messages.menu').' '.trans('messages.configuration').' '.trans('messages.updated')];
        if(config('config.application_setup_info') && defaultRole()){
        	$setup_data = Helper::setupInfo();
        	$response['setup_data'] = $setup_data;
        }
		return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	}

	public function setupComplete(Request $request){

        $setup = \App\Setup::orderBy('id','asc')->get();
        $setup_total = 0;
        $setup_completed = 0;
        foreach($setup as $value){
            $setup_total += config('setup.'.$value->module.'.weightage');
            if($value->completed)
                $setup_completed += config('setup.'.$value->module.'.weightage');
        }
        $setup_percentage = ($setup_total) ? round(($setup_completed/$setup_total) * 100) : 0;

        if($setup_percentage != 100 && !config('config.application_setup_info')){
        	if($request->has('ajax_submit')){
		        $response = ['message' => 'We are back!', 'status' => 'success']; 
		        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		    }
        	return redirect('/dashboard');
        }

        $config = \App\Config::firstOrNew(['name' => 'application_setup_info']);
        $config->value = 0;
        $config->save();

    	if($request->has('ajax_submit')){
	        $response = ['message' => trans('messages.welcome_back'), 'status' => 'success']; 
	        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	    }
        return redirect('/dashboard');
	}
}
?>