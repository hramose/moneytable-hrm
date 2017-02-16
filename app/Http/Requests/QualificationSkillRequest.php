<?php
namespace App\Http\Requests;
use App\Http\Requests\Request;

class QualificationSkillRequest extends Request
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
        $qualification_skill = $this->route('qualification_skill');
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
                    'name' => 'required|unique:qualification_skills,name'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name' => 'required|unique:qualification_skills,name,'.$qualification_skill->id
                ];
            }
            default:break;
        }
    }
}
