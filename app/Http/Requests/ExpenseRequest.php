<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;

class ExpenseRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'expense_head_id' => 'required',
            'amount' => 'required|numeric|min:0',
            'date_of_expense' => 'required|date',
            'attachments'=> 'mimes:'.config('config.allowed_upload_file')
        ];
    }

    public function attributes()
    {
        return[
            'expense_head_id' => 'expense head',
        ];

    }
}
