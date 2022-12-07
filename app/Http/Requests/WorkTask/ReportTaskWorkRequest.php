<?php

namespace App\Http\Requests\WorkTask;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReportTaskWorkRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            //
            'id' => 'nullable',
            'Date' => 'required|date_format:d/m/Y',
            'ScreenName' => 'nullable|string|max:50',
            'TypeWork' => 'required|string',
            'Contents' => 'required|string|max:200',
            'WorkingTime' => 'required|numeric|between:0.1,8',
            'Progressing' => 'required|numeric|between:0.1,100',
            'Timedelay' => 'nullable|numeric|gte:0',
            'Timesoon' => 'nullable|numeric|gte:0',
            'Note' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
       return [
           'Date.date_format' => 'Định dạng Ngày không hợp lệ',
        //    'ScreenName.required' => 'Vui lòng điền Tên màn hình',
           'ScreenName.max' => 'Tên màn hình không được vượt quá 50 ký tự',
           'Contents.max' => 'Nội dung không được vượt quá 200 ký tự',
           'WorkingTime.between' => 'Thời gian làm lớn hơn 0 và nhỏ hơn hoặc bằng 8 ',
           'Progressing.between' => 'Tiến độ phải trong khoảng từ 0% đến 100%',
           'Timedelay.numeric' => 'Giờ trễ là ký tự chữ số',
           'Timedelay.gte' => 'Giờ trễ phải lớn hơn hoặc bằng 0',
           'Timesoon.numeric' => 'Giờ vượt là ký tự chữ số',
           'Timesoon.gte' => 'Giờ vượt phải lớn hơn hoặc bằng 0'
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
