<?php
namespace App;
use Eloquent;

class LeaveType extends Eloquent {

	protected $fillable = [
							'name'
						];
	protected $primaryKey = 'id';
	protected $table = 'leave_types';

    public function leave()
    {
        return $this->hasMany('App\Leave'); 
    }
}
