<?php
namespace App\Http\Controllers;
use DB;
use Auth;
use Entrust;
use File;
use Mail;
use App\Classes\Helper;
use Illuminate\Http\Request;
use App\Http\Requests\VerifyPurchaseRequest;

class DashboardController extends Controller
{
    use BasicController;

   public function index(){

        $date = date('Y-m-d');

        $time = date('H:i:s');
        $my_shift = Helper::getShift($date);

        if(isset($my_shift) && $my_shift->overnight && $time >= '00:00:00' && $time <= $my_shift->out_time){
            $date = date('Y-m-d',strtotime($date . ' -1 days'));
            $my_shift = Helper::getShift($date);
        }

        $count_office_shift = \App\OfficeShift::whereIsDefault(1)->count();

        $clocks = \App\Clock::whereUserId(Auth::user()->id)
            ->where('date','=',$date)
            ->orderBy('clock_in')
            ->get();

        $count_clock = $clocks->count();

        $clock_rest = $clocks->slice($count_clock - 1);

        $clock = \App\Clock::where('user_id','=',Auth::user()->id)
            ->where('date','=',$date)
            ->where('clock_out','=',null)
            ->count();

        $user_count = \App\User::count();
        $dept_count = \App\Department::count();
        $task_count = \App\Task::where('progress','<',100)->count();
        $present_count = \App\Clock::where('date','=',date('Y-m-d'))->groupBy('user_id')->get();
        $leave_count = 0;
        $employee = \App\User::find(Auth::user()->id);
        
        $start_date = date('Y-m-d', strtotime('-7 days'));
        $end_date = date('Y-m-d');
        $graph_holidays = \App\Holiday::where('date','>=',$start_date)->where('date','<=',$end_date)->get();

        $leaves = \App\Leave::whereStatus('approved')->where(function($query) use($start_date,$end_date) {
            $query->whereBetween('from_date',array($start_date,$end_date))
            ->orWhereBetween('to_date',array($start_date,$end_date))
            ->orWhere(function($query1) use($start_date,$end_date) {
                $query1->where('from_date','<',$start_date)
                ->where('to_date','>',$end_date);
            });
        })->get();

        $leave_approved = array();
        foreach($leaves as $leave){
            $leave_approved_dates = ($leave->approved_date) ? explode(',',$leave->approved_date) : [];
            foreach($leave_approved_dates as $leave_approved_date)
                $leave_approved[] = $leave_approved_date;
        }

        $leave_approved = array_count_values($leave_approved);
            
        $daily_employee_attendance = [];
        for($i=6;$i>=0;$i--){
            $day = date('Y-m-d', strtotime('-'.$i.' days'));
            $present = \App\Clock::where('date','=',$day)->distinct('user_id')->count('user_id');
            $graph_holiday = $graph_holidays->whereLoose('date',$day)->first();
            $leave = array_key_exists($day, $leave_approved) ? $leave_approved[$day] : 0;
            $absent = ($graph_holiday) ? 0 : \App\User::whereStatus('active')->count()-$leave-$present;
            $dayw = date('d-M-Y',strtotime($day)).(($graph_holiday) ? ' (Holiday)' : '');
            $daily_employee_attendance[] = array($dayw,$present,$absent,$leave);

            if($i == 0)
                $leave_count = $leave;
        }

        if(defaultRole())
        $tasks = \App\Task::where('progress','<',100)->orderBy('created_at','desc')->take('5')->get();
        else
        $tasks = \App\Task::where('progress','<',100)->whereHas('user', function($q){
                $q->where('user_id','=',Auth::user()->id);
            })->orderBy('created_at','desc')->take('5')->get();

        if($clock)
            $clock_status = 'clock_out';
        else
            $clock_status = 'clock_in';

        $announcements = \App\Announcement::with('designation')->where('from_date','<=',date('Y-m-d'))
                ->where('to_date','>=',date('Y-m-d'))->whereHas('designation',function($query) {
                    $query->where('designation_id','=',Auth::user()->designation_id);
                })->orWhere(function ($query)  { $query->doesntHave('designation'); })->get();

        $child_designation = Helper::childDesignation(Auth::user()->designation_id,1);
        $child_staff_count = \App\User::whereIn('designation_id',$child_designation)->count();

        $child_users = \App\User::whereIn('designation_id',$child_designation)->pluck('id')->all();

        if(Entrust::can('message_all_employee'))
            $compose_users = \App\User::where('id','!=',Auth::user()->id)->get()->pluck('full_name_with_designation', 'id')->all();
        elseif(Entrust::can('message_subordinate'))
            $compose_users = \App\User::whereIn('id',$child_users)->get()->pluck('full_name_with_designation', 'id')->all();
        else
            $compose_users = [];
        
        array_push($child_users,Auth::user()->id);

        $all_birthdays = \App\Profile::whereBetween( DB::raw('dayofyear(date_of_birth) - dayofyear(curdate())'), [0,config('config.celebration_days')])
            ->orWhereBetween( DB::raw('dayofyear(curdate()) - dayofyear(date_of_birth)'), [0,config('config.celebration_days')])
            ->orderBy('date_of_birth','asc')
            ->get();

        $all_anniversaries = \App\Profile::whereBetween( DB::raw('dayofyear(date_of_joining) - dayofyear(curdate())'), [0,config('config.celebration_days')])
            ->orWhereBetween( DB::raw('dayofyear(curdate()) - dayofyear(date_of_joining)'), [0,config('config.celebration_days')])
            ->orderBy('date_of_joining','asc')
            ->get();

        $celebrations = array();
        foreach($all_birthdays as $all_birthday){
            $number = date('Y') - date('Y',strtotime($all_birthday->date_of_birth));
            $celebrations[strtotime(date('d M',strtotime($all_birthday->date_of_birth)))] = array(
                'icon' => 'fa-birthday-cake',
                'title' => getDateDiff($all_birthday->date_of_birth) ? : date('d M',strtotime($all_birthday->date_of_birth)),
                'date' => $all_birthday->date_of_birth,
                'number' => $number.'<sup>'.daySuffix($number).'</sup>'.' '.trans('messages.birthday'),
                'id' => $all_birthday->User->id,
                'name' => $all_birthday->User->full_name,
                'designation' => $all_birthday->User->Designation->full_designation
            );
        }

        foreach($all_anniversaries as $all_anniversary){
            $number = date('Y') - date('Y',strtotime($all_anniversary->date_of_joining));
            if($number)
            $celebrations[strtotime(date('d M',strtotime($all_anniversary->date_of_joining)))] = array(
                'icon' => 'fa-gift',
                'title' => getDateDiff($all_anniversary->date_of_joining) ? : date('d M',strtotime($all_anniversary->date_of_joining)),
                'date' => $all_anniversary->date_of_joining,
                'id' => $all_anniversary->User->id,
                'number' => $number.'<sup>'.daySuffix($number).'</sup>'.' '.trans('messages.work_anniversary'),
                'name' => $all_anniversary->User->full_name,
                'designation' => $all_anniversary->User->Designation->full_designation
            );
        }

        ksort($celebrations);

        $birthdays = \App\Profile::whereNotNull('date_of_birth')->orderBy('date_of_birth','asc')->get();
        $work_anniversaries = \App\Profile::whereNotNull('date_of_joining')->orderBy('date_of_joining','asc')->get();

        $holidays = \App\Holiday::all();
        $todos = \App\Todo::where('user_id','=',Auth::user()->id)
            ->orWhere(function ($query)  {
                $query->where('user_id','!=',Auth::user()->id)
                    ->where('visibility','=','public');
            })->get();

        $events = array();
        foreach($birthdays as $birthday){
            $start = date('Y').'-'.date('m-d',strtotime($birthday->date_of_birth));
            $title = trans('messages.birthday').' : '.$birthday->User->full_name_with_designation;
            $color = '#133edb';
            $events[] = array('title' => $title, 'start' => $start, 'color' => $color);
        }
        foreach($work_anniversaries as $work_anniversary){
            $start = date('Y').'-'.date('m-d',strtotime($work_anniversary->date_of_joining));
            $title = trans('messages.work_anniversary').' : '.$work_anniversary->User->full_name_with_designation;
            $color = '#8B1A1A';
            $events[] = array('title' => $title, 'start' => $start, 'color' => $color);
        }
        foreach($holidays as $holiday){
            $start = $holiday->date;
            $title = trans('messages.holiday').' : '.$holiday->description;
            $color = '#1e5400';
            $events[] = array('title' => $title, 'start' => $start, 'color' => $color);
        }
        foreach($todos as $todo){
            $start = $todo->date;
            $title = trans('messages.to_do').' : '.$todo->title.' '.$todo->description;
            $color = '#ff0000';
            $url = '/todo/'.$todo->id.'/edit';
            $events[] = array('title' => $title, 'start' => $start, 'color' => $color, 'url' => $url);
        }

        $tree = array();
        $designations = \App\Designation::all();
        foreach ($designations as $designation){
            $tree[$designation->id] = array(
                'parent_id' => $designation->top_designation_id,
                'name' => $designation->full_designation
            );
        }

        $assets = ['graph','calendar','rte','tour'];
        $menu = ['dashboard'];

        return view('dashboard',compact(
            'assets','clock_status','user_count','dept_count','task_count','leave_count','employee','compose_users','daily_employee_attendance','tasks','announcements',
            'birthdays','holidays','events','tree','child_staff_count','my_shift','clocks','celebrations','menu','count_office_shift'
            ));
   }

   public function recentActivity(){

        $child_designation = Helper::childDesignation(Auth::user()->designation_id,1);
        $child_users = \App\User::whereIn('designation_id',$child_designation)->pluck('id')->all();
        array_push($child_users,Auth::user()->id);
        $activities = \App\Activity::whereIn('user_id',$child_users)->orderBy('created_at','desc')->take('50')->get();

        $data = '<ul class="media-list">';
        foreach($activities as $activity){

            if($activity->activity == 'activity_added')
                $description = trans('messages.new').' '.trans('messages.'.$activity->module).' '.trans('messages.'.$activity->activity);
            else
                $description = trans('messages.'.$activity->module).' '.trans('messages.'.$activity->activity);

            $data .= '<li class="media">
                <a class="pull-left" href="#">'.Helper::getAvatar($activity->user_id).'</a>
                <div class="media-body">
                  <h4 class="media-heading"><a href="#">'.$activity->User->full_name.'</a> <span class="pull-right"><small>'.showDateTime($activity->created_at).'</small></span></h4>
                  <p><i class="fa '.(config('icons.'.$activity->module) ? : 'fa-bars').' icon"></i> '.$description.'</p>
                </div>
            </li>';
        }
        $data .= '</ul>';

        return $data;
   }

   public function sidebar(){
        return view('layouts.sidebar_menu');
   }

}