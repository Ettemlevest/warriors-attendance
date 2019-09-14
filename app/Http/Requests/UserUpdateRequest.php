<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Auth::user()->owner) {
            return true;
        }

        if (Auth::user()->id === $this->route('user')->id) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'max:255'],
            'nickname' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email', 'unique:users,email,'.$this->route('user')->id],
            'password' => ['nullable'],
            'owner' => ['required', 'boolean'],
            'photo' => ['nullable', 'image'],
        ];
    }
}
