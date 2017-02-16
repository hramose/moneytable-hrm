<?php
namespace App;
use Eloquent;

class TicketNote extends Eloquent {

	protected $fillable = [
							'note',
							'user_id',
							'ticket_id'
						];
	protected $primaryKey = 'id';
	protected $table = 'ticket_notes';

	public function ticket()
    {
        return $this->belongsTo('App\Ticket');
    }

	public function user()
    {
        return $this->belongsTo('App\User');
    }
}
