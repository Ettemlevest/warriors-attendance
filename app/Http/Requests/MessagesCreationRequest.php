<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MessagesCreationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (! Auth::user()->owner) {
            return false;
        }

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
            'title' => ['sometimes', 'string', 'max:255'],
            'body'  => ['required', 'string'],
            'showed_from' => ['sometimes', 'date_format:Y-m-d'],
            'showed_to' => ['sometimes', 'date_format:Y-m-d'],
        ];
    }
}
