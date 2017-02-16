<?php
namespace App;
use Eloquent;

class Location extends Eloquent {

	protected $fillable = [
							'name',
                            'top_location_id'
						];
	protected $primaryKey = 'id';
	protected $table = 'locations';

    protected function children()
    {
        return $this->hasMany('App\Location','top_location_id','id');
    }

    protected function parent()
    {
        return $this->belongsTo('App\Location','top_location_id','id');
    }
}
