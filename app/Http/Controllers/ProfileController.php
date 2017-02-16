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

    public function getLeave(Request $request){
      $user_id = $request->input('user_id') ? : Auth::user()->id;
      $contract = Helper::getContract($user_id);
      $leave_types = \App\LeaveType::all();
      $raw_data = array();
      $data = '';

      $user_leaves = \App\Leave::whereUserId($user_id)->get();
      $leave_applied = $user_leaves->count();
      $leave_approved = $user_leaves->whereLoose('status','approved')->count();
      $leave_rejected = $user_leaves->whereLoose('status','rejected')->count();
      $leave_pending = $user_leaves->whereLoose('status','pending')->count();

      if(!$contract)
        return '<div class="alert alert-danger"><i class="fa fa-times icon"></i> '.trans('messages.no_data_found').'</div>';

      $data .= '<div class="table-responsive">
                  <table class="table table-stripped table-bordered table-hover show-table">
                    <tbody>
                      <tr>
                        <th><i class="fa fa-bell info"></i> '.trans('messages.leave').' '.trans('messages.applied').'</th>
                        <td><span class="badge badge-info">'.$leave_applied.'</span></td>
                      </tr>
                      <tr>
                        <th><i class="fa fa-thumbs-up success"></i> '.trans('messages.leave').' '.trans('messages.approved').'</th>
                        <td><span class="badge badge-success">'.$leave_approved.'</span></td>
                      </tr>
                      <tr>
                        <th><i class="fa fa-thumbs-down danger"></i> '.trans('messages.leave').' '.trans('messages.rejected').'</th>
                        <td><span class="badge badge-danger">'.$leave_rejected.'</span></td>
                      </tr>
                      <tr>
                        <th><i class="fa fa-hourglass warning"></i> '.trans('messages.leave').' '.trans('messages.pending').'</th>
                        <td><span class="badge badge-warning">'.$leave_pending.'</span></td>
                      </tr>
                    </tbody>
                  </table>
                </div><br />';

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