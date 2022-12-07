<?php

namespace App\Http\Requests;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class WorkingScheduleRequest extends FormRequest
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
            'id'                    =>  'integer|min:1',
//            'user_id'               =>  'integer|min:1',
            'date_work'             =>  'required|date_format:d/m/Y',
//            'time_working'          =>  'required|integer',
            'stime'                 =>  'required|date_format:H:i',
            'etime'                 =>  'required|date_format:H:i',
            'content'               =>  'required|string',
            'address'               =>  'string|nullable',
            'note'                  =>  'string|nullable',
            // 'assign_id'             =>  'required|array',
        ];
    }

    public function messages()
    {
        return [
            'id.min' => 'id không hợp lệ',
//            'user_id.min' => 'Người tạo không hợp lệ',
            'date_work.required' => 'Ngày không được để trống',
            'date_work.date_format' => 'Ngày không hợp lệ',
            'etime.required' => 'Giờ kết thúc không được để trống',
            'etime.date_format' => 'Giờ kết thúc không hợp lệ',
            'stime.required' => 'Giờ bắt đầu không được để trống',
            'stime.date_format' => 'Giờ bắt đầu không hợp lệ',
//            'time_working.required' => 'Thời gian không được để trống',
//            'time_working.integer' => 'Thời gian không hợp lệ',
            'content.required' => 'Nội dung không được để trống',
            // 'assign_id.required' => 'Nhân viên thực hiện không được để trống',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        throw new HttpResponseException(AdminController::jsonErrors($errors->first()));
        // throw new HttpResponseException(AdminController::responseApi(422, $errors->first()));
    }
}
