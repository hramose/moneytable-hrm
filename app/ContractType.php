<?php
namespace App;
use Eloquent;

class ContractType extends Eloquent {

	protected $fillable = [
							'name'
						];
	protected $primaryKey = 'id';
	protected $table = 'contract_types';

	public function contract()
    {
        return $this->hasMany('App\Contract');
    }
}
