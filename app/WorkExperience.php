<?php
namespace App;
use Eloquent;

class WorkExperience extends Eloquent {

	protected $fillable = [
							'user_id',
							'company_name',
							'from_date',
							'to_date',
							'post',
							'description'
						];
	protected $primaryKey = 'id';
	protected $table = 'work_experiences';

    public function user()
    {
        return $this->belongsTo('App\User'); 
    }
}
