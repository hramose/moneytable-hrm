<?php

namespace App\Http\Requests;
use App\Http\Requests\Request;

class BankAccountRequest extends Request
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
        $bank_account = $this->route('bank_account');
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                $rules = [
                    'account_number' => 'required|unique:bank_accounts,account_number',
                    'account_name' => 'required',
                    'bank_name' => 'required'
                ];
                return $rules;
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'account_name' => 'required',
                    'bank_name' => 'required',
                    'account_number' => 'required|unique:bank_accounts,account_number,'.$bank_account->id
                ];
            }
            default:break;
        }
    }
}
