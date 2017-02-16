<?php
namespace App\Http\Controllers;
use App\Classes\Helper;
use Illuminate\Http\Request;
use File;

Class MailController extends Controller{
    use BasicController;

	public function index(Request $request){

		return $request->all();

		// if(!Helper::verifyCsrf($token))
		// 	return redirect('/dashboard')->withErrors(trans('messages.invalid_token'));

		if($type == 'ticket')
			return	$this->ticket($id);
		elseif($type == 'employee')
			return $this->employee($id);
		else
			return redirect('/dashboard')->withErrors(trans('messages.invalid_link'));
	}

	public function copyTemplate(Request $request){
		$path = base_path().'/config/template/'.config('config.domain').'/'.$request->input('key');
		$content = '';
		if(File::exists($path) && $request->input('key'))
			$content = File::get($path);

		$employee = \App\User::find($request->input('id'));
		$subject = config('template.'.$request->input('key').'.subject');

		$response = ['body' => $content, 'subject' => $subject, 'status' => 'success']; 
        return response()->json($response, 200, array('Access-Controll-Allow-Origin' => '*'));
	}

	public function ticket($id){

	}

	public function employee($id){
	}
}
