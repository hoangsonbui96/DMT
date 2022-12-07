<?php

namespace App\Http\Requests;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DailyReportOneRequest extends FormRequest
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
            'id'          => 'integer|min:1',
            'Date'        => 'required|string',
            'ProjectID'   => 'required|integer',
            'ScreenName'  => 'nullable|string',
            'TypeWork'    => 'required|string',
            'Contents'    => 'required|string',
            'WorkingTime' => 'required|numeric|between:0,24',
            'Progressing' => 'required|numeric|between:0,100',
            'Note'        => 'nullable|string',
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
            'id.min' => 'id không hợp lệ',
            'Date.required' => 'Ngày làm việc không được để trống',
            'ProjectID.required' => 'Chưa chọn dự án',
            'TypeWork.required' => 'Chưa chọn kiểu công việc',
            'Contents.required' => 'Chưa điền nội dung báo cáo',
            'WorkingTime.required' => 'Thời gian làm việc không được để trống',
            'WorkingTime.between' => 'Thời gian làm việc không hợp lệ',
            'Progressing.required' => 'Tiến độ không được để trống',
            'Progressing.between' => 'Tiến độ không hợp lệ'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(AdminController::responseApi(422, $errors->first()));
    }
}
