<?php

namespace App\Http\Requests;
use Illuminate\Support\Facades\Log;

class UserRoleUpdateRequest extends ApiFormRequest
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
        Log::debug("ID:: " . request()->id);
        return [
            'role' => 'required|string|max:100'. request()->id,
        ];
    }
}