<?php
namespace App;
use Eloquent;

class MessageCategory extends Eloquent {

	protected $fillable = [
							'name'
						];
	protected $primaryKey = 'id';
	protected $table = 'message_categories';

    public function message()
    {
        return $this->hasMany('App\Message'); 
    }
}
