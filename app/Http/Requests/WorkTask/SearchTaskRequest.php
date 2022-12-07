<?php

namespace App\Http\Requests\WorkTask;

use App\WorkTask;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchTaskRequest extends FormRequest
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
            'StartDate' => 'nullable|date_format:d/m/Y',
            'EndDate' => 'nullable|date_format:d/m/Y'
        ];
    }

    public function messages()
    {
        return [
            'StartDate.date_format' => 'Ngày Bắt đầu sai định dạng',
            'EndDate.date_format' => 'Ngày Kết thúc sai định dạng',
            'EndDate.after_or_equal' => 'Ngày Kết thúc phải sau ngày Bắt đầu'
        ];
    }

    public function withValidator($validator)
    {
        if (!$validator->fails()) {
            $validator->after(function ($validator) {
                if (!is_null($this->StartDate) && !is_null($this->EndDate)){
                    $startDate = Carbon::parse($this->StartDate);
                    $endDate = Carbon::parse($this->EndDate);
                    if ($endDate->lt($startDate)){
                        $validator->errors()->add("EndDate", "Ngày kết thúc phải sau ngày bắt đầu");
                    }
                }
            });
        }
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
