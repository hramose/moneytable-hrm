<?php
namespace App;
use Eloquent;

class Salary extends Eloquent {

	protected $fillable = [
							'contract',
							'salary_type_id',
							'amount'
						];
	protected $primaryKey = 'id';
	protected $table = 'salary';

	public function contract()
    {
        return $this->belongsTo('App\Contract');
    }

	public function salaryType()
    {
        return $this->belongsTo('App\SalaryType');
    }
}
