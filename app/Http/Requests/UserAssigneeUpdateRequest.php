<?php

namespace App\Http\Requests;

class UserAssigneeUpdateRequest extends ApiFormRequest
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
            'user_id' => 'required|numeric',
            'level_id' => 'required|numeric',
            'activity_ids' => 'required|json|array',
        ];
    }
}