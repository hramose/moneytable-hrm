<?php

namespace App\Http\Requests;
use App\Http\Requests\Request;

class EmployeeRequest extends Request
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
        $employee = $this->route('employee');
        return [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'role_id' => 'sometimes|required',
                    'designation_id' => 'sometimes|required',
                    'email' => 'required|unique:users,email,'.$employee->id,
                    'date_of_birth' => 'sometimes|date',
                    'gender' => 'required',
                    'marital_status' => 'required',
                    'date_of_birth' => 'date',
                    'date_of_joining' => 'sometimes|date|after:date_of_birth',
                    'date_of_leaving' => 'sometimes|date|after:date_of_joining',
                    'employee_code' => 'sometimes|required|unique:profile,employee_code,'.$employee->Profile->id.',id'
                ];
    }

    public function attributes()
    {
        return[
            'role_id' => 'role',
        ];

    }
}
