<?php
namespace App;
use Eloquent;

class Ticket extends Eloquent {

	protected $fillable = [
							'user_id',
							'subject',
							'description',
							'priority',
							'status'
						];
	protected $primaryKey = 'id';
	protected $table = 'tickets';

	public function user()
    {
        return $this->belongsToMany('App\User','ticket_user','ticket_id','user_id');
    }

	public function userAdded()
    {
        return $this->belongsTo('App\User','user_id');
    }

	public function ticketComment()
    {
        return $this->hasMany('App\TicketComment');
    }

	public function ticketNote()
    {
        return $this->hasMany('App\TicketNote');
    }
    
	public function ticketAttachment()
    {
        return $this->hasMany('App\TicketAttachment');
    }
}
