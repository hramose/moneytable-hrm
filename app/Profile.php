<?php
namespace App;
use Eloquent;

class Profile extends Eloquent {

	protected $fillable = [
							'employee_code',
							'contact_number',
							'date_of_birth',
							'gender',
							'marital_status',
							'date_of_joining',
							'date_of_leaving',
							'photo',
							'facebook_link',
							'twitter_link',
							'blogger_link',
							'linkedin_link',
							'googleplus_link',
							'user_id'
						];
	protected $primaryKey = 'id';
	protected $table = 'profile';

	public function user() {
    	return $this->belongsTo('App\User');
	}

}
