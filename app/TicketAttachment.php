<?php
namespace App;
use Eloquent;

class TicketAttachment extends Eloquent {

	protected $fillable = [
							'title',
							'description',
							'ticket_id',
							'user_id',
							'attachments'
						];
	protected $primaryKey = 'id';
	protected $table = 'ticket_attachments';

	public function ticket()
    {
        return $this->belongsTo('App\Ticket');
    }

	public function user()
    {
        return $this->belongsTo('App\User');
    }
}
