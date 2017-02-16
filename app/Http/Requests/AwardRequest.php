<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;

class AwardRequest extends Request
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
            'award_type_id' => 'required',
            'user_id' => 'required|array',
            'cash' => 'numeric|min:0',
            'month' => 'required',
            'year' => 'required',
            'date_of_award' => 'required|date'
        ];
    }

    public function attributes()
    {
        return[
            'award_type_id' => 'award type',
            'user_id' => 'user',
        ];

    }
}
