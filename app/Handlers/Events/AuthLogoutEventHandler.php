<?php

namespace App\Handlers\Events;

use App\Events;
use App\Classes\Helper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuthLogoutEventHandler{
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
        if(config('config.auto_clock_authentication')){
            $url = url('/')."/clock/out";
            $postData = array(
                'datetime' => date('Y-m-d H:i:s'),
                'user_id' => \Auth::user()->id,
                'api' => 1
            );
            Helper::callCurl($url,$postData);
        }
        $this->logActivity(['module' => 'logout','activity' => 'activity_logged_out']);
    }
}
