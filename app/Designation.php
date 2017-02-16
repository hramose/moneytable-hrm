<?php
namespace App;
use Eloquent;

class Designation extends Eloquent {

	protected $fillable = [
							'department_id',
							'name',
                            'top_designation_id'
						];
	protected $primaryKey = 'id';
	protected $table = 'designations';

	protected function department()
    {
        return $this->belongsTo('App\Department','department_id','id');
    }

    protected function children()
    {
        return $this->hasMany('App\Designation','top_designation_id','id');
    }

    protected function parent()
    {
        return $this->belongsTo('App\Designation','top_designation_id','id');
    }

	protected function profile()
    {
        return $this->hasMany('App\Profile');
    }

	protected function job()
    {
        return $this->hasMany('App\Job');
    }

	public function announcement()
    {
        return $this->belongsToMany('App\Announcement','announcement_designation','announcement_id','designation_id');
    }

    public function getFullDesignationAttribute()
    {
        return $this->name . " (" . ucfirst($this->Department->name).")";
    }

}
