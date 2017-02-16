<?php
namespace App;
use Eloquent;

class Ip extends Eloquent {

	protected $fillable = [
							'start',
							'end'
						];
	protected $primaryKey = 'id';
	protected $table = 'allowed_ips';
}
