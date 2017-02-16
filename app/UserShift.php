<?php
namespace App;
use Eloquent;

class UserShift extends Eloquent {

	protected $fillable = [
							'user_id',
							'office_shift_id',
							'from_date',
							'to_date'
						];
	protected $primaryKey = 'id';
	protected $table = 'user_shifts';

	public function user()
    {
        return $this->belongsTo('App\User');
    }

	public function officeShift()
    {
        return $this->belongsTo('App\OfficeShift');
    }
}
