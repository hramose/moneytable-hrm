<?php
namespace App;
use Eloquent;

class DocumentType extends Eloquent {

	protected $fillable = [
							'name'
						];
	protected $primaryKey = 'id';
	protected $table = 'document_types';

    public function document()
    {
        return $this->hasMany('App\Document'); 
    }
}
