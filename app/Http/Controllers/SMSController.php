<?php
namespace App\Http\Controllers;
use DB;
use Entrust;
use Illuminate\Http\Request;
use Validator;
use App\Classes\Helper;

Class SMSController extends Controller{
    use BasicController;

	public function index($type = 'designation'){

		if(!Entrust::can('manage_sms'))
			return redirect('/dashboard')->withErrors(trans('messages.permission_denied'));

		if($type == 'designation')
        $receivers = \App\Designation::all()->pluck('full_designation','id')->all();
       	else
        $receivers = \App\User::all()->pluck('full_name_with_designation','id')->all();

        $type_detail = ($type == 'designation') ? trans('messages.designation') : trans('messages.individual_employee') ;

        $menu = ['sms'];
		return view('sms.index',compact('type','receivers','type_detail','menu'));
	}

	public function sendEmployeeSMS(Request $request, $id){
		$user = \App\User::find($id);

		$validation = Validator::make($request->all(),[
				'sms' => 'required'
				]);

		if($validation->fails())
			return redirect()->back()->withInput()->withErrors($validation->messages());

      	$response = Helper::sendSMS($user->Profile->contact_number,$request->input('sms'));

      	if($response == 1)
      		return redirect()->back()->withSuccess(trans('messages.sms').' '.trans('messages.sent'));
      	else
      		return redirect()->back()->withErrors($response);
	}

	public function store()
	{
		return redirect()->back();
	}
}
?>