<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DailyReportRequest extends FormRequest
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
            'id.*'          => 'integer|min:1',
            'Date'          => 'required|array',
            'Date.*'        => 'required|string',
            'ProjectID'     => 'required|array',
            'ProjectID.*'   => 'required|integer',
            'ScreenName'    => 'required|array',
            'ScreenName.*'  => 'required|string',
            'TypeWork'      => 'required|array',
            'TypeWork.*'    => 'required|string',
            'Contents'      => 'required|array',
            'Contents.*'    => 'required|string',
            'WorkingTime'   => 'required|array',
            'WorkingTime.*' => 'required|numeric|between:0,24|gt:0',
            'Progressing'   => 'required|array',
            'Progressing.*' => 'required|numeric|between:0,100|gt:0',
            'Note'          => 'required|array',
            'Note.*'        => 'nullable|string',
            'issue' => 'nullable|regex:/^[a-zA-Z]+$/u|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.*.min' => 'id không hợp lệ',
            'Date.*.required' => 'Ngày làm việc không được để trống',
            'ProjectID.*.required' => 'Chưa chọn dự án',
			'ScreenName.*.required' => 'Chưa nhập tên màn hình hoặc chức năng',
            'TypeWork.*.required' => 'Chưa chọn kiểu công việc',
            'Contents.*.required' => 'Chưa điền nội dung báo cáo',
            'WorkingTime.*.required' => 'Thời gian làm việc không được để trống',
            'WorkingTime.*.between' => 'Thời gian làm việc không hợp lệ',
            'WorkingTime.*.between' => 'Thời gian làm việc lớn hơn 0 và nhỏ hỏn hơn bằng 24',
            'Progressing.*.required' => 'Tiến độ không được để trống',
            'Progressing.*.between' => 'Tiến độ không hợp lệ',
            'Progressing.*.between' => 'Tiến độ phải lớn hơn 0 và nhỏ hơn bằng 100'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $errors->first(),
        ], 200));
    }
}
