<?php
namespace App;
use Eloquent;

class EducationLevel extends Eloquent {

	protected $fillable = [
							'name'
						];
	protected $primaryKey = 'id';
	protected $table = 'education_levels';

	public function qualification()
    {
        return $this->hasMany('App\Qualification');
    }
}
