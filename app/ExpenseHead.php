<?php
namespace App;
use Eloquent;

class ExpenseHead extends Eloquent {

	protected $fillable = [
							'head'
						];
	protected $primaryKey = 'id';
	protected $table = 'expense_heads';


	public function expense()
    {
        return $this->hasMany('App\Expense');
    }
}
