<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Classes\Helper;
use Entrust;
use Form;

trait BasicController {

    public function logActivity($data) {
    	$data['user_id'] = isset($data['user_id']) ? $data['user_id'] : ((\Auth::check()) ? \Auth::user()->id : null);
    	$data['ip'] = \Request::getClientIp();
        $data['secondary_id'] = isset($data['secondary_id']) ? $data['secondary_id'] : null;
    	$activity = \App\Activity::create($data);
    }

    public function logEmail($data){
        $data['to_address'] = $data['to'];
        unset($data['to']);
        $data['from_address'] = config('mail.from.address');
        \App\Email::create($data);
    }

    public function designationAccessible($designation){
        if(Entrust::can('manage_all_designation') || (Entrust::can('manage_subordinate_designation') && Helper::isChild($designation->id)))
            return 1;
        else
            return 0;
    }

    public function employeeAccessible($employee){
    	if(Entrust::can('manage_all_employee') || (Entrust::can('manage_subordinate_employee') && Helper::isChild($employee->designation_id)))
    		return 1;
    	else
    		return 0;
    }

    public function expenseAccessible($expense){
        if(Entrust::can('manage_all_expense') || (Entrust::can('manage_subordinate_expense') && Helper::isChild($expense->User->designation_id)))
            return 1;
        else
            return 0;
    }

    public function AnnouncementAccessible($announcement){
        if(Entrust::can('manage_all_announcement') || (Entrust::can('manage_subordinate_announcement') && (Helper::isChild($announcement->User->designation_id) || $announcement->user_id == \Auth::user()->id)))
            return 1;
        else
            return 0;
    }

    public function awardAccessible($award){
        if(Entrust::can('manage_all_award') || (Entrust::can('manage_subordinate_award') && (Helper::isChild($award->ByUser->designation_id) || $award->user_id == \Auth::user()->id)) || $award->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function taskAccessible($task){
        if(Entrust::can('manage_all_task') || (Entrust::can('manage_subordinate_task') && (Helper::isChild($task->UserAdded->designation_id) || $task->user_id == \Auth::user()->id)) || $task->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function ticketAccessible($ticket){
        if(Entrust::can('manage_all_ticket') || (Entrust::can('manage_subordinate_ticket') && (Helper::isChild($ticket->UserAdded->designation_id) || $ticket->user_id == \Auth::user()->id)) || $ticket->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function leaveAccessible($leave){
        if(Entrust::can('manage_all_leave') || (Entrust::can('manage_subordinate_leave') && (Helper::isChild($leave->User->designation_id) || $leave->user_id == \Auth::user()->id)) || $leave->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function jobAccessible($job){
        if(Entrust::can('manage_all_job') || (Entrust::can('manage_subordinate_job') && (Helper::isChild($job->User->designation_id) || $job->user_id == \Auth::user()->id)) || $job->user_id == \Auth::user()->id)
            return 1;
        else
            return 0;
    }

    public function jobApplicationAccessible($job_application){
        if(Entrust::can('manage_job_application') && (Entrust::can('manage_all_job') || (Entrust::can('manage_subordinate_job') && (Helper::isChild($job_application->Job->designation_id) || $job_application->Job->user_id == \Auth::user()->id)) || $job_application->Job->user_id == \Auth::user()->id))
            return 1;
        else
            return 0;
    }
}