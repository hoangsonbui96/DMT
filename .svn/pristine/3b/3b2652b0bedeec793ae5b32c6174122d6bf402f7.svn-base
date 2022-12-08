<?php

namespace App\Http\Requests\WorkTask;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class WorkTaskSearchRequest extends FormRequest
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
            //
            'keywords' => 'nullable|string',
            'errorIndex' => 'nullable|integer|gt:0',
            'errorType' => 'nullable|string',
            'Date' => 'nullable|array',
            'Date.*' => 'nullable|date_format:d/m/Y'
        ];
    }

    public function messages()
    {
        return [
            'errorIndex.integer' => 'Thời gian trong khoảng phải là số nguyên',
            'errorIndex.gt' => 'Thời gian trong khoảng phải lớn hơn 0',
            'Date.*.date_format' => 'Sai định dạng thời gian'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => $errors->first(),
        ], 422));
    }

}
