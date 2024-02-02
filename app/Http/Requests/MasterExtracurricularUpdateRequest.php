<?php

namespace App\Http\Requests;

class MasterExtracurricularUpdateRequest extends ApiFormRequest
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
           'organization_id' => 'required|numeric',
            'extracurricular_name' => 'required',
        ];
    }
}