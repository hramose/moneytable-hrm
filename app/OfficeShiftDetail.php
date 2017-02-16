<?php
namespace App;
use Eloquent;

class OfficeShiftDetail extends Eloquent {

	protected $fillable = [
							'office_shift_id',
							'day',
							'in_time',
							'out_time',
							'overnight'
						];
	protected $primaryKey = 'id';
	protected $table = 'office_shift_details';

	public function officeShift()
    {
        return $this->belongsTo('App\OfficeShift');
    }
}
