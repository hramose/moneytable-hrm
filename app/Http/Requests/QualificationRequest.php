<?php

namespace App\Http\Requests;
use App\Http\Requests\Request;

class QualificationRequest extends Request
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
        $qualification = $this->route('qualification');
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
                    'institute_name' => 'required',
                    'from_year' => 'required|numeric',
                    'to_year' => 'required'
                ];
                return $rules;
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'institute_name' => 'required',
                    'from_year' => 'required|numeric',
                    'to_year' => 'required'
                ];
            }
            default:break;
        }
    }
}
