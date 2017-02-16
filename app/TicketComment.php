<?php
namespace App;
use Eloquent;

class TicketComment extends Eloquent {

	protected $fillable = [
							'ticket_id',
							'comment',
							'user_id'
						];
	protected $primaryKey = 'id';
	protected $table = 'ticket_comments';

	public function ticket()
    {
        return $this->belongsTo('App\Ticket');
    }

	public function user()
    {
        return $this->belongsTo('App\User');
    }
}
