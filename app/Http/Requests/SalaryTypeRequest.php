<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;

class SalaryTypeRequest extends Request
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
        $salary_type = $this->route('salary_type');
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
                    'salary_type' => 'required|in:earning,deduction',
                    'head' => 'required|unique:salary_types,head'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'salary_type' => 'required|in:earning,deduction',
                    'head' => 'required|unique:salary_types,head,'.$salary_type->id
                ];
            }
            default:break;
        }
    }
}
