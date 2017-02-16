<?php

namespace App\Http\Requests;
use App\Http\Requests\Request;

class JobRequest extends Request
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
        $job = $this->route('job');
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
                    'title' => 'required|unique:jobs,title',
                    'date_of_closing' => 'required|date',
                    'job_type' => 'required',
                    'no_of_post' => 'required|integer|min:1',
                    'designation_id' => 'required',
                    'location' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'date_of_closing' => 'required|date',
                    'job_type' => 'required',
                    'no_of_post' => 'required|integer|min:1',
                    'designation_id' => 'required',
                    'location' => 'required',
                    'title' => 'required|unique:jobs,title,'.$job->id
                ];
            }
            default:break;
        }
    }

    public function attributes()
    {
        return[
            'designation_id' => 'designation',
        ];

    }
}
