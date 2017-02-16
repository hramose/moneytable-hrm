<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class RegisterRequest extends Request
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
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'designation_id' => 'required',
                    'date_of_joining' => 'required|date',
                    'role_id' => 'required',
                    'password' => 'required|confirmed|min:6',
                    'employee_code' => 'required|unique:profile',
                    'email' => 'required|email|max:255|unique:users',
                    'username' => 'required|min:4|max:255|alpha_num|unique:users',
                    'password_confirmation' => 'required|same:password'
                ];
    }

    public function attributes()
    {
        return[
            'role_id' => 'role',
            'designation_id' => 'designation',
        ];

    }
}
