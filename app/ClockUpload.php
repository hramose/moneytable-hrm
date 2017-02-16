<?php
namespace App;
use Eloquent;

class ClockUpload extends Eloquent {

	protected $fillable = [
							'user_id',
							'date',
							'filename'
						];
	protected $primaryKey = 'id';
	protected $table = 'clock_uploads';

	public function user()
    {
        return $this->belongsTo('App\User');
    }
}
