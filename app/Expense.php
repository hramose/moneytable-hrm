<?php
namespace App;
use Eloquent;

class Expense extends Eloquent {

	protected $fillable = [
							'expense_head_id',
							'user_id',
							'amount',
							'date_of_expense',
							'remarks'
						];
	protected $primaryKey = 'id';
	protected $table = 'expenses';

	public function expenseHead()
    {
        return $this->belongsTo('App\ExpenseHead');
    }

    public function user(){
    	return $this->belongsTo('App\User');
    }
}
