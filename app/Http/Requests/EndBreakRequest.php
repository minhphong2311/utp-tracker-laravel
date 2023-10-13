<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Breaks;

class EndBreakRequest extends FormRequest
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
            'startbreak_id' => 'required',
            'endbreak' => [
                'required',
                function ($attribute, $value, $fail) {
                    $break = Breaks::findOrFail($this->startbreak_id);
                    if (strtotime($value) < strtotime($break['startbreak'])) {
                        $fail('Must be greater than the start time');
                    }
                },
            ],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([$validator->errors()], 422));
    }

    public function messages()
    {
        return [
            'startbreak_id.required' => 'The :attribute field is required.',
            'endbreak.required' => 'The :attribute field is required.',
        ];
    }
}
