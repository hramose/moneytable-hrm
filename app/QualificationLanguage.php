<?php
namespace App;
use Eloquent;

class QualificationLanguage extends Eloquent {

	protected $fillable = [
							'name'
						];
	protected $primaryKey = 'id';
	protected $table = 'qualification_languages';

	public function qualification()
    {
        return $this->hasMany('App\Qualification');
    }
}
