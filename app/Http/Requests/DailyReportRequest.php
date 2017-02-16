<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;

class DailyReportRequest extends Request
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
        $daily_report = $this->route('daily_report');
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
                    'date' => 'required|unique_with:daily_reports,user_id',
                    'user_id' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'user_id' => 'required',
                    'date' => 'required|unique_with:daily_reports,user_id,'.$daily_report->id
                    
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
