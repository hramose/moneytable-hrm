<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ContractRequest extends Request
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
        $contract = $this->route('contract');
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
                    'from_date' => 'required|date|before_equal:to_date',
                    'to_date' => 'required|date',
                    'contract_type_id' => 'required',
                    'title' => 'required|unique_with:contracts,user_id'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'from_date' => 'required|date|before_equal:to_date',
                    'to_date' => 'required|date',
                    'contract_type_id' => 'required',
                    'title' => 'required|unique_with:contracts,user_id,'.$contract->id
                ];
            }
            default:break;
        }
    }
}
