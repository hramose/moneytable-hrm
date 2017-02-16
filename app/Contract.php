<?php
namespace App;
use Eloquent;

class Contract extends Eloquent {

	protected $fillable = [
							'user_id',
							'contract_type_id',
							'title',
							'from_date',
							'to_date',
							'designation_id',
							'description'
						];
	protected $primaryKey = 'id';
	protected $table = 'contracts';

	public function contractType()
    {
        return $this->belongsTo('App\ContractType');
    }

    public function designation(){
    	return $this->belongsTo('App\Designation');
    }

	public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function userLeave(){
    	return $this->hasMany('App\UserLeave');
    }

    public function salary(){
    	return $this->hasMany('App\Salary');
    }

    public function getFullContractNameAttribute(){
    	return $this->title.' ('.$this->ContractType->name.')';
    }

    public function getFullContractDetailAttribute(){
    	return $this->title.' ('.$this->ContractType->name.') from '.showDate($this->from_date).' to '.showDate($this->to_date);
    }

    public function getFullContractDateAttribute(){
        return showDate($this->from_date).' to '.showDate($this->to_date);
    }
}
