<?php
namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Classes\Helper;
use File;
use Auth;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\AccountUpdateRequest;
set_time_limit(0);

Class AccountController extends Controller{
    use BasicController;

    public function verifyPurchase(){

    	$data = verifyPurchase();
    	if($data['status'] == 'success')
    		return redirect('/');
    	return view('install.verify');
    }

    public function updateApp(){

    	if(!config('code.mode'))
    		return redirect('/dashboard');

    	if(!checkDBConnection())
    		return view('install.update');
    	else
    		return redirect('/');
    }

    public function postUpdateApp(AccountUpdateRequest $request){

    	if(!is_connected()){
	        if($request->has('ajax_submit')){
	            $response = ['message' => trans('messages.check_internet_connection'), 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
    		return redirect('/update')->withErrors(trans('messages.check_internet_connection'));
    	}
    	
		$purchase_code = $request->input('purchase_code');
		$envato_username = $request->input('envato_username');
		$email = $request->input('email');
		$mysql_database = $request->input('mysql_database');
		$data = installPurchase($purchase_code,$envato_username,$email);

		if($data['status'] != 'success'){
	        if($request->has('ajax_submit')){
	            $response = ['message' => $data['message'], 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/update')->withInput()->withErrors($data['message']);
		}


		if (!is_writable('../config/db.php')){
	        if($request->has('ajax_submit')){
	            $response = ['message' => 'db.php file is not writable.', 'status' => 'error']; 
	            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	        }
			return redirect('/update')->withInput()->withErrors('db.php file is not writable.');
		}
		else{
			$link = @mysqli_connect($request->input('hostname'), $request->input('mysql_username'), $request->input('mysql_password'));

			if (!$link){
		        if($request->has('ajax_submit')){
		            $response = ['message' => 'Connection could not be established.', 'status' => 'error']; 
		            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
		        }
				return redirect('/update')->withInput()->withErrors('Connection could not be established.');
			}
			else{
					mysqli_select_db($link,$request->input('mysql_database'));
					$count_table_query = mysqli_query($link,"show tables");
					$count_table = mysqli_num_rows($count_table_query);

					if (!is_file('../database/'.config('code.build').'.sql')){
				        if($request->has('ajax_submit')){
				            $response = ['message' => 'Database file not found.', 'status' => 'error']; 
				            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
				        }
						return redirect('/update')->withInput()->withErrors('Database file not found.');
					}
					elseif(!$count_table){
				        if($request->has('ajax_submit')){
				            $response = ['message' => 'No existing table found in database. Please check database.', 'status' => 'error']; 
				            return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
				        }
						return redirect('/update')->withInput()->withErrors('No existing table found in database. Please check database.');
					}
					else{
						$templine = '';
						$lines = file('../database/'.config('code.build').'.sql');
						foreach ($lines as $line)
						{
							if (substr($line, 0, 2) == '--' || $line == '')
								continue;
							$templine .= $line;
							if (substr(trim($line), -1, 1) == ';')
							{
								mysqli_query($link,$templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
								$templine = '';
							}	
						}

						mysqli_query($link,'SET FOREIGN_KEY_CHECKS = 0');
						mysqli_query($link,'truncate table `permission_role`');
						mysqli_query($link,'truncate table `permissions`');

						$query = "select id from roles where name='admin' ";
						$check = mysqli_query($link,$query);
						$row = mysqli_fetch_array($check);
						$role_id = $row['id'];
						mysqli_query($link,"update roles set is_hidden='1' where id='$role_id' ");

						$query1 = "select id,designation_id from users";
						$check1 = mysqli_query($link,$query1);
						$row1 = mysqli_fetch_array($check1);
						$user_id = $row1['id'];
						$designation_id = $row1['designation_id'];
						mysqli_query($link,"update users set is_hidden='1' where id='$user_id' ");
						mysqli_query($link,"update designations set is_hidden='1' where id='$designation_id' ");
						$query2 = "select departments.id from departments,designations where designations.department_id = departments.id and designations.id = '$designation_id'";
						$check2 = mysqli_query($link,$query2);
						$row2 = mysqli_fetch_array($check2);
						$department_id = $row2['id'];
						mysqli_query($link,"update departments set is_hidden='1' where id='$department_id' ");
						
						$db = config('db');
						$db['hostname'] = $request->input('hostname');
						$db['database'] = $request->input('mysql_database');
						$db['username'] = $request->input('mysql_username');
						$db['password'] = $request->input('mysql_password');
						write2Config($db,'db');
						
						$config = config('code');
						$config['purchase_code'] = $purchase_code;
						write2Config($config,'code');
						
						return redirect('/')->withSuccess('Updated successfully.');
					}
				}
			}
    }

    public function postVerifyPurchase(Request $request){

    	if(!is_connected())
    		return redirect()->back()->withErrors(trans('messages.check_internet_connection'));

    	$data = verifyPurchase();

    	if($data['status'] == 'status')
    		return redirect('/');

    	$purchase_code = $request->input('purchase_code');
    	$envato_username = $request->input('envato_username');
		$data = installPurchase($request->input('purchase_code'),$request->input('envato_username'));
		if($data['status'] == 'success'){
			$config = config('code');
			$config['purchase_code'] = $purchase_code;
			write2Config($config,'code');
			return redirect('/login')->withSuccess($data['message']);
		}
		else
			return redirect('/verify-purchase')->withErrors($data['message']);
    }

   public function releaseLicense(){
    	if(!config('code.mode'))
    		return redirect('/dashboard');
    	
        if(!is_connected())
            return redirect('/dashboard')->withErrors(trans('messages.check_internet_connection'));

        $data = verifyPurchase();
        if($data['status'] == 'error'){
            Auth::logout();
            return redirect('/')->withErrors('Your purchase license is invalid.');
        }

        $data = releaseLicense();
        if($data['status'] == 'success'){
            $config = config('code');
            $config['purchase_code'] = null;
            write2Config($config,'code');
            Auth::logout();
            return redirect('/')->withSuccess('Your license is released successfully. You can now install it into another system.');
        }
   }

	public function checkUpdate(){
    	if(!config('code.mode'))
    		return redirect('/dashboard');

    	$data = (config('code.build') && is_connected()) ? getUpdate() : [];

    	$data = json_decode($data,true);
    	return view('install.check_update',compact('data'));
	}

	public function index(){

    	if(checkDBConnection())
			return redirect('/');

		$error = 0;
		$checks = array();

		if(dirname($_SERVER['REQUEST_URI']) != '/' && str_replace('\\', '/', dirname($_SERVER['REQUEST_URI'])) != '/')
			$checks[] = array('type' => 'error', 'message' => 'You are trying to install this application in a subfolder "'.dirname($_SERVER['REQUEST_URI']).'"');
		else
			$checks[] = array('type' => 'success', 'message' => ' Installation directory "'.$_SERVER['SERVER_NAME'].'"');

		$server = $_SERVER['SERVER_SOFTWARE'];
		$server_is_ok = ( (stripos($server, 'Apache') === 0) || (stripos($server, 'nginx') === 0) );
		$checks[] = $this->check($server_is_ok, sprintf('Web server is suitable (%s)', $server), 'You should change the server to Apache or Nginx', true);
		$checks[] = $this->check($this->my_version_compare(phpversion(), '5.5.9', '>='), sprintf('PHP version is at least 5.5.9 (%s)', 'Current Version is '. phpversion()), 'Current version is '.phpversion(), true);
		$checks[] = $this->check(extension_loaded('fileinfo'), 'Fileinfo PHP extension loaded', 'Install and enable Fileinfo extension', true);
		$checks[] = $this->check(extension_loaded('mcrypt'), 'Mcrypt PHP extension loaded', 'Install and enable Mcrypt extension', true);
		$checks[] = $this->check(extension_loaded('openssl'), 'OpenSSL PHP extension loaded', 'Install and enable Mcrypt extension', true);
		$checks[] = $this->check(extension_loaded('tokenizer'), 'Tokenizer PHP extension loaded', 'Install and enable Mcrypt extension', true);
		$checks[] = $this->check(extension_loaded('mbstring'), 'Mbstring PHP extension loaded', 'Install and enable Mcrypt extension', true);
		$checks[] = $this->check(extension_loaded('zip'), 'Zip archive PHP extension loaded', 'Install and enable Mcrypt extension', true);
		$checks[] = $this->check(class_exists('PDO'), 'PDO is installed', 'Install PDO (mandatory for Eloquent)', true);

		foreach($checks as $check)
			if($check['type'] == 'error')
				$error++;

		return view('install.index',compact('checks','error'));
	}

	public function is_cli() {
	  return !isset($_SERVER['HTTP_HOST']);
	}

	public function my_version_compare($ver1, $ver2, $operator = null)
	{
	    $p = '#(\.0+)+($|-)#';
	    $ver1 = preg_replace($p, '', $ver1);
	    $ver2 = preg_replace($p, '', $ver2);
	    return isset($operator) ? 
	        version_compare($ver1, $ver2, $operator) : 
	        version_compare($ver1, $ver2);
	}

	public function check($boolean, $message, $help = '', $fatal = false) {
	  if($boolean)
	  	return array('type' => 'success','message' => $message);
	  else
	  	return array('type' => 'error', 'message' => $help);
	}

	public function get_ini_path() {
	  if ($path = get_cfg_var('cfg_file_path')) {
	    return $path;
	  }
	  return 'WARNING: not using a php.ini file';
	}

	public function store(AccountRequest $request){

    	if(!is_connected())
    		return redirect()->back()->withErrors(trans('messages.check_internet_connection'));
    	
		$purchase_code = $request->input('purchase_code');
		$envato_username = $request->input('envato_username');
		$registered_email = $request->input('email');
		$mysql_database = $request->input('mysql_database');
		$data = installPurchase($purchase_code,$envato_username,$registered_email);

		if($data['status'] != 'success')
			return redirect()->back()->withInput()->withErrors($data['message']);

		if (!is_writable('../config/db.php'))
			return redirect()->back()->withInput()->withErrors('db.php file is not writable.');
		else{
			$link = @mysqli_connect($request->input('hostname'), $request->input('mysql_username'), $request->input('mysql_password'));
			
			if (!$link)
				return redirect()->back()->withInput()->withErrors('Connection could not be established.');
			else{
					mysqli_select_db($link,$request->input('mysql_database'));

					$count_table_query = mysqli_query($link,"show tables");
					$count_table = mysqli_num_rows($count_table_query);

					if (!is_file('../database/database.sql'))
						return redirect()->back()->withInput()->withErrors('Database file not found.');
					elseif($count_table)
						return redirect()->back()->withInput()->withErrors('Table already exists. Installation needs empty database.');
					else{
							$templine = '';
							$lines = file('../database/database.sql');
							foreach ($lines as $line)
							{
								if (substr($line, 0, 2) == '--' || $line == '')
									continue;
								$templine .= $line;
								if (substr(trim($line), -1, 1) == ';')
								{
									mysqli_query($link,$templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
									$templine = '';
								}	
							}
							
							$username = $request->input('username');
							$password = bcrypt($request->input('password'));
							$email = $request->input('email');
							$first_name = $request->input('first_name');
							$last_name = $request->input('last_name');
							$default_role = config('constants.default_role');
							$default_department = config('constants.default_department');
							$default_designation = config('constants.default_designation');
							
							mysqli_query($link, "insert into roles(name,is_hidden) values('$default_role','1')");
							mysqli_query($link, "insert into departments(name,is_hidden) values('$default_department','1')");
							mysqli_query($link, "insert into designations(department_id,name,is_hidden) values('1','$default_designation',1)");
							mysqli_query($link, "insert into users(email,username,password,is_hidden,designation_id,first_name,last_name) values('$email','$username','$password','1','1','$first_name','$last_name') ");
							mysqli_query($link, "insert into profiles(user_id) values('1') ");	
							mysqli_query($link, "insert into role_user(user_id,role_id) values('1','1') ");

    						$db = config('db');
    						$db['hostname'] = $request->input('hostname');
    						$db['database'] = $request->input('mysql_database');
    						$db['username'] = $request->input('mysql_username');
    						$db['password'] = $request->input('mysql_password');
							write2Config($db,'db');

							$config = config('code');
							$config['purchase_code'] = $purchase_code;
							write2Config($config,'code');
							return redirect('/')->withSuccess('Installed successfully.');
						}
					}
						
				}
			}

}
?>