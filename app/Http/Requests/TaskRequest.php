<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;

class TaskRequest extends Request
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
        $task = $this->route('task');
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
                    'title' => 'required|unique:tasks',
                    'start_date' => 'required|date|before_equal:due_date',
                    'due_date' => 'required|date',
                    'description' => 'required',
                    'user_id' => 'required',
                    'hours' => 'numeric'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'title' => 'required|unique:tasks,title,'.$task->id.',id',
                    'start_date' => 'required|date|before_equal:due_date',
                    'due_date' => 'required|date',
                    'description' => 'required',
                    'user_id' => 'required',
                    'hours' => 'numeric'
                ];
            }
            default:break;
        }
    }

    public function attributes()
    {
        return[
            'user_id' => 'user',
        ];

    }
}
