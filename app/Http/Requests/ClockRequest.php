<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClockRequest extends FormRequest
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
            'event_wp_id' => 'required',
            'event_name' => 'required',

            'user_wp_id' => 'required',
            'name' => 'required',
            'email' => 'required',

            'clockin' => 'required',
            'clockin_photo' => 'image',
            'clockin_location' => 'required',
            'clockin_address' => 'required',

            'clockout' => 'required',
            'clockout_photo' => 'image',
            'clockout_location' => 'required',
            'clockout_address' => 'required',
            'total_time' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([$validator->errors()], 422));
    }
}
