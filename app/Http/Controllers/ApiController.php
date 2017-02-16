<?php
namespace App\Http\Controllers;
use App\Classes\Helper;
use Auth;

Class ApiController extends Controller{
    use BasicController;

    public function listEmployee($auth_token = null){

    	if($auth_token == '' || $auth_token == null)
            return response()->json(['error' => '100']);

    	$user = \App\User::whereAuthToken($auth_token)->first();

    	if(!$user)
            return response()->json(['error' => '101']);

    	if(!$user->can('list_employee'))
    		return response()->json(['error' => 'You don\'t have permission to list employee.']);

    	if($user->can('manage_all_employee'))
    		$users = \App\User::all();
    	elseif($user->can('manage_subordinate_employee')){
          	$childs = Helper::childDesignation($user->designation_id,1);
          	$users = \App\User::whereIn('designation_id',$childs)->get();
    	} else
    		$users = Auth::user();

    	return $users;
    }

    public function clock($auth_token,$emp_code){
        if($auth_token == '' || $auth_token == null)
            return response()->json(['type' => 'error','error_code' => '100']);

        if($emp_code == '' || $emp_code == null)
            return response()->json(['type' => 'error','error_code' => '102']);

        $user = \App\User::whereAuthToken($auth_token)->first();

        if(!$user)
            return response()->json(['type' => 'error','error_code' => '101']);

        $clock_user_profile = \App\Profile::whereEmployeeCode($emp_code)->first();

        if(!$clock_user_profile)
            return response()->json(['type' => 'error','error_code' => '103']);

        $clock_user = $clock_user_profile->User;
        $user_id = $clock_user->id;

        if($clock_user->status != 'active')
            return response()->json(['type' => 'error','error_code' => '104']);

        return response()->json(['type' => 'success','user_id' => $user_id]);
    }

    public function clockIn($auth_token = null,$emp_code = null){

        $response = $this->clock($auth_token,$emp_code);
        $data = json_decode($response->content(),true);
        if($data['type'] != 'success')
            return $response;
        else
            $user_id = $data['user_id'];

        $url = url('/')."/clock/in";
        $postData = array(
            'datetime' => date('Y-m-d H:i:s'),
            'user_id' => $user_id,
            'api' => 1
        );

        return Helper::callCurl($url,$postData);
    }

    public function clockOut($auth_token = null,$emp_code = null){
        
        $response = $this->clock($auth_token,$emp_code);
        $data = json_decode($response->content(),true);
        if($data['type'] != 'success')
            return $response;
        else
            $user_id = $data['user_id'];

        $url = url('/')."/clock/out";
        $postData = array(
            'datetime' => date('Y-m-d H:i:s'),
            'user_id' => $user_id,
            'api' => 1
        );

        return Helper::callCurl($url,$postData);
    }
}