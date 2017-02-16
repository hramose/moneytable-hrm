<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;

class ExpenseHeadRequest extends Request
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
        $expense_head = $this->route('expense_head');
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'head' => 'required|unique:expense_heads,head'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'head' => 'required|unique:expense_heads,head,'.$expense_head->id
                ];
            }
            default:break;
        }
    }
}
