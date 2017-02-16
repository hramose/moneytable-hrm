<?php
namespace App;
use Eloquent;

class UserLeave extends Eloquent {

	protected $fillable = [
							'leave_type_id',
							'leave_count',
							'contract_id',
						];
	protected $primaryKey = 'id';
	protected $table = 'user_leaves';

	public function user()
    {
        return $this->belongsTo('App\User');
    }

	public function contract()
    {
        return $this->belongsTo('App\Contract');
    }

	public function leaveType()
    {
        return $this->belongsTo('App\LeaveType');
    }
}
