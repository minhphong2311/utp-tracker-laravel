<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Clocks;

class StartBreakRequest extends FormRequest
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
            'startbreak' => [
                'required',
                function ($attribute, $value, $fail) {
                    $clock = Clocks::findOrFail($this->clock_id);
                    if (strtotime($value) < strtotime($clock['clockin'])) {
                        $fail('Must be greater than the ClockIn time');
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
            'clock_id.required' => 'The :attribute field is required.',
            'startbreak.required' => 'The :attribute field is required.',
        ];
    }
}
