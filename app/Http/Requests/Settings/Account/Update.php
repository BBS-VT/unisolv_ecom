<?php

namespace App\Http\Requests\Settings\Account;

use App\Http\Requests\Rules\MatchOldPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'avatar' => 'mimes:jpeg,jpg,png,gif',
            'FullName' => 'required|string|max:190',
            'old_password' => ['sometimes', 'nullable', 'string', 'min:8', new MatchOldPassword],
            'new_password' => 'required_with:old_password|nullable|string|min:8',
            'email' => [
                'required',
                'string',
                'email',
                'max:190',
                Rule::unique('users')->ignore(request()->user()->id),
            ],
            'roles'    => [
                'required',
                'array',
            ],
        ];
    }

}
