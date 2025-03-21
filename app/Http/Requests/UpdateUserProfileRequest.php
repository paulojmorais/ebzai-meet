<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
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
            'username' => ['required', 'string', 'min:3', 'max:20', 'alpha_dash', 'unique:users,username,'.($this->route('id') ?? $this->user()->id)],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email,'.($this->route('id') ?? $this->user()->id)],
            'avatar' => 'nullable|file|mimes:png,jpg|max:2048',
        ];
    }
}
