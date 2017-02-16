<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Classes\Helper;
use File;

class ScheduleJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduleJob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'common task perform';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        defaultDB();
        $users = \App\User::all();

        foreach($users as $user){
            if(isset($user->Profile->date_of_leaving) && $user->Profile->date_of_leaving < date('Y-m-d'))
                $user->status = 'in-active';
            else
                $user->status = 'active';
            $user->save();

            $contract = Helper::getContract($user->id);
            if($contract && isset($contract->designation_id)){
                $user->designation_id = $contract->designation_id;
                $user->save();
            }
        }
        include('app/Classes/Dumper.php');
        $data = backupDatabase();
        if($data['status'] == 'success'){
            $filename = $data['filename'];
            File::move($filename, 'public/'.config('constants.upload_path.backup').$filename);
            \App\Backup::create(['file' => $filename]);
        }
    }
}
