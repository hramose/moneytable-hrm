<?php
namespace App;
use Eloquent;

class Document extends Eloquent {

	protected $fillable = [
							'user_id',
							'document_type_id',
							'date_of_expiry',
							'title',
							'description',
							'attachments',
							'status'
						];
	protected $primaryKey = 'id';
	protected $table = 'documents';

    public function user()
    {
        return $this->belongsTo('App\User'); 
    }

    public function DocumentType()
    {
        return $this->belongsTo('App\DocumentType'); 
    }
}
