<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClockInRequest extends FormRequest
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
            'clockin_photo' => 'required|image',
            'clockin_location' => 'required',
            'clockin_address' => 'required',

            'created_at' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([$validator->errors()], 422));
    }


    public function messages()
    {
        return [
            'event_wp_id.required' => 'The :attribute field is required.',
            'event_name.required' => 'The :attribute field is required.',

            'user_wp_id.required' => 'The :attribute field is required.',
            'name.required' => 'The :attribute field is required.',
            'email.required' => 'The :attribute field is required.',

            'clockin.required' => 'The :attribute field is required.',
            'clockin_photo.image' => 'The :attribute must be an image.',
            'clockin_location.required' => 'The :attribute field is required.',
            'clockin_address.required' => 'The :attribute field is required.',
        ];
    }
}
