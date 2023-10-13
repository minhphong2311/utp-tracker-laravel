<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangeClockRequest extends FormRequest
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
            'clock_id' => 'required',
            'change_clockin' => 'required',
            'change_clockout' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) < strtotime($this->change_clockin)) {
                        $fail('Must be greater than ClockIn time');
                    }
                },
            ],
            'comment' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([$validator->errors()], 422));
    }


    public function messages()
    {
        return [
            'clock_id.required' => 'The :attribute field is required.',
            'change_clockin.required' => 'The :attribute field is required.',
            'change_clockout.required' => 'The :attribute field is required.',
            'comment.required' => 'The :attribute field is required.',
        ];
    }
}
