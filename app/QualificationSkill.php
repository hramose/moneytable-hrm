<?php
namespace App;
use Eloquent;

class QualificationSkill extends Eloquent {

	protected $fillable = [
							'name'
						];
	protected $primaryKey = 'id';
	protected $table = 'qualification_skills';

	public function qualification()
    {
        return $this->hasMany('App\Qualification');
    }
}
