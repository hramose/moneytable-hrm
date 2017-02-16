<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;

class DocumentRequest extends Request
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
                'document_type_id' => 'required',
                'date_of_expiry' => 'required|date',
                'title' => 'required',
                'attachments' => 'required|mimes:'.config('config.allowed_upload_file')
            ];
    }

    public function attributes()
    {
        return[
            'document_type_id' => 'document type',
        ];

    }
}
