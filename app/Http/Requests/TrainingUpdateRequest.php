<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TrainingUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'place' => ['required', 'string', 'max:255'],
            'start_at_day' => ['required', 'date_format:Y-m-d'],
            'start_at_time' => ['required', 'date_format:H:i'],
            'length' => ['required', 'integer'],
            'max_attendees' => ['required', 'integer'],
        ];
    }
}
