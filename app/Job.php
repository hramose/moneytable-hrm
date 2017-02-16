<?php
namespace App;
use Eloquent;

class Job extends Eloquent {

	protected $fillable = [
							'title',
							'location',
							'job_type',
							'date_of_closing',
							'no_of_post',
							'description',
							'designation_id',
							'user_id'
						];
	protected $primaryKey = 'id';
	protected $table = 'jobs';

	public function user()
    {
        return $this->belongsTo('App\User');
    }

	public function designation()
    {
        return $this->belongsTo('App\Designation');
    }

    public function getFullJobTitleAttribute()
    {
        return $this->title . " for " . ucfirst($this->Designation->full_designation);
    }

	public function interviewer()
    {
        return $this->belongsToMany('App\User','job_interviewer','job_id','user_id');
    }
}
