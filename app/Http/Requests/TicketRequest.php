<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;

class TicketRequest extends Request
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
        $ticket = $this->route('ticket');
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
                    'subject' => 'required|unique_with:tickets,user_id',
                    'priority' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'priority' => 'required',
                    'subject' => 'required|unique_with:tickets,user_id,'.$ticket->id
                ];
            }
            default:break;
        }
    }
}
