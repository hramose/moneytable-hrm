<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Classes\Helper;
use App\Http\Controllers\BasicController;

class UploadAttendance extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, BasicController;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $clock_upload_id;

    public function __construct($clock_upload_id)
    {
         $this->clock_upload_id = $clock_upload_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

    }
}