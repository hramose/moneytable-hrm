<?php
namespace App;
use Eloquent;

class JobApplication extends Eloquent {

	protected $fillable = [
							'job_id',
							'name',
							'email',
							'contact_number',
							'source',
							'resume',
							'date_of_application',
							'status'
						];
	protected $primaryKey = 'id';
	protected $table = 'job_applications';

	public function job()
    {
        return $this->belongsTo('App\Job');
    }

	public function jobApplicationInterview()
    {
        return $this->hasMany('App\JobApplicationInterview');
    }
}
