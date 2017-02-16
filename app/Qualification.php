<?php
namespace App;
use Eloquent;

class Qualification extends Eloquent {

	protected $fillable = [
							'user_id',
							'institute_name',
							'from_year',
							'to_year',
							'education_level_id',
							'qualification_language_id',
							'qualification_skill_id'
						];
	protected $primaryKey = 'id';
	protected $table = 'qualifications';

    public function user()
    {
        return $this->belongsTo('App\User'); 
    }

    public function educationLevel()
    {
        return $this->belongsTo('App\EducationLevel'); 
    }

    public function qualificationLanguage()
    {
        return $this->belongsTo('App\QualificationLanguage'); 
    }

    public function qualificationSkill()
    {
        return $this->belongsTo('App\QualificationSkill'); 
    }
}
