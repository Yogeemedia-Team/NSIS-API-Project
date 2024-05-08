<?php

namespace App\Http\Requests;

class MasterGradeUpdateRequest extends ApiFormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'grade_name' => 'required',
            'organization_id' => 'required|numeric',
        ];
    }
}