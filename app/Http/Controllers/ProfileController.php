<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Classes\Helper;

class ProfileController extends Controller
{
    use BasicController;

  public function index(){
    
  }

   public function changePassword(){
        return view('auth.change_password');
   }

   public function doChangePassword(Request $request)
   {
		$this->validate($request, [
            'old_password' => 'required|valid_password',
            'new_password' => 'required|confirmed|different:old_password|min:4',
            'new_password_confirmation' => 'required|different:old_password|same:new_password'
        ]);
        $credentials = $request->only(
                'new_password', 'new_password_confirmation'
        );

        $user = Auth::user();
        
        $user->password = bcrypt($credentials['new_password']);
        $user->save();
        return redirect('change_password')->withSuccess('Password has been changed.');    
    }

    public function getLeave(Request $request){
      $user_id = $request->input('user_id') ? : Auth::user()->id;
      $contract = Helper::getContract($user_id);
      $leave_types = \App\LeaveType::all();
      $raw_data = array();
      $data = '';

      if(!$contract)
        return '<div class="alert alert-danger"><i class="fa fa-times icon"></i> '.trans('messages.no_data_found').'</div>';

      $data .= '<p>'.trans('messages.contract_period').': <strong>'.showDate($contract->from_date).' '.trans('messages.to').' '.showDate($contract->to_date).'</strong></p>';
      foreach($leave_types as $leave_type){
        $name = $leave_type->name;
        $used = ($contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->count()) ? $contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->first()->leave_used : 0;
        $allotted = ($contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->count()) ? $contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->first()->leave_count : 0;

        if($allotted)
        $raw_data[] = array(
            'name' => $leave_type->name,
            'used' => $used,
            'allotted' => $allotted
          );

        if($allotted){
          $used_percentage = ($allotted) ? ($used/$allotted)*100 : 0;
          $data .= '<p><strong>'.$name.' ('.$used.'/'.$allotted.')'.'</strong></p>
          <div class="progress">
            <div class="progress-bar progress-bar-'.Helper::storageColor($used_percentage).'" role="progressbar" aria-valuenow="'.$used_percentage.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$used_percentage.'%;"></div>
          </div>';
        }
      }
      return $data;
    }
}