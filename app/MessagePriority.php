<?php
namespace App;
use Eloquent;

class MessagePriority extends Eloquent {

	protected $fillable = [
							'name'
						];
	protected $primaryKey = 'id';
	protected $table = 'message_priorities';

    public function message()
    {
        return $this->hasMany('App\Message'); 
    }
}
