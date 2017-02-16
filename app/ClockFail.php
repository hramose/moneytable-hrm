<?php
namespace App;
use Eloquent;

class ClockFail extends Eloquent {

	protected $fillable = [
							'clock_upload_id'
						];
	protected $primaryKey = 'id';
	protected $table = 'clock_fails';
	public $timestamps = false;

	public function clockUpload()
    {
        return $this->belongsTo('App\ClockUpload');
    }
}
