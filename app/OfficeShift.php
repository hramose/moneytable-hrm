<?php
namespace App;
use Eloquent;

class OfficeShift extends Eloquent {

	protected $fillable = [
							'name'
						];
	protected $primaryKey = 'id';
	protected $table = 'office_shifts';

	public function officeShiftDetail()
    {
        return $this->hasMany('App\OfficeShiftDetail');
    }
}
