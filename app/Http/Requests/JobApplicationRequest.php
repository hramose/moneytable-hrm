<?php

namespace App\Http\Requests;
use App\Http\Requests\Request;

class JobApplicationRequest extends Request
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
        $job_application = $this->route('job_application');
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
                    'job_id' => 'required|unique_with:job_applications,email',
                    'name' => 'required',
                    'email' => 'email|required',
                    'contact_number' => 'required',
                    'resume' => 'mimes:'.config('config.application_format')
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name' => 'required',
                    'email' => 'email|required',
                    'contact_number' => 'required',
                    'job_id' => 'required|unique_with:job_applications,email,'.$job_application->id,
                    'resume' => 'mimes:'.config('config.application_format')
                ];
            }
            default:break;
        }
    }

    public function attributes()
    {
        return[
            'job_id' => 'job',
        ];

    }
}
