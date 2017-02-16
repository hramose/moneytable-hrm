<?php

namespace App\Handlers\Events;

use App\Events;
use App\Classes\Helper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class AuthLoginEventHandler{
    use \App\Http\Controllers\BasicController;
    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Events  $event
     * @return void
     */
    public function handle($event)
    {
        \Auth::user()->last_login = \Auth::user()->last_login_now;
        \Auth::user()->last_login_ip = \Auth::user()->last_login_ip_now;
        \Auth::user()->last_login_now = new \DateTime;
        \Auth::user()->last_login_ip_now = \Request::getClientIp();
        \Auth::user()->save();
        $this->logActivity(['module' => 'login','activity' => 'activity_logged_in']);

        if(config('config.auto_clock_authentication')){
            $url = url('/')."/clock/in";
            $postData = array(
                'datetime' => date('Y-m-d H:i:s'),
                'user_id' => \Auth::user()->id,
                'api' => 1
            );
            Helper::callCurl($url,$postData);
        }
    }
}
