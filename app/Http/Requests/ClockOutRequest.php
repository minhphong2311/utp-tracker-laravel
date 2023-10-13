<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClockOutRequest extends FormRequest
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
            'clockin_id' => 'required',

            'clockout' => 'required',
            'clockout_photo' => 'required|image',
            'clockout_location' => 'required',
            'clockout_address' => 'required',

            'updated_at' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([$validator->errors()], 422));
    }

    public function messages()
    {
        return [
            'clockin_id.required' => 'The :attribute field is required.',

            'clockout.required' => 'The :attribute field is required.',
            'clockout_photo.image' => 'The :attribute must be an image.',
            'clockout_location.required' => 'The :attribute field is required.',
            'clockout_address.required' => 'The :attribute field is required.',
        ];
    }
}
