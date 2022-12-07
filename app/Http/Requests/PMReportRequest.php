<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PMReportRequest extends FormRequest
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
            'id.*'                          =>      'integer|min:1',
            'Content'                       =>      'required|string',
            'StartDate'                     =>      'required|date_format:d/m/Y',
            'EndDate'                       =>      'nullable|date_format:d/m/Y|after_or_equal:StartDate',
            'NameProject'                   =>      'nullable|array',
            'NameProject.*'                 =>      'required|string'
        ];
    }

    public function messages()
    {
        return [
            'id.*.min'                      =>     'id không hợp lệ',
            'Content.required'              =>     'Chưa điền tiêu đề báo cáo',
            'StartDate.required'            =>     'Không được bỏ trống Ngày bắt đầu',
            'StartDate.date_format'         =>     'Sai định dạng Ngày bắt đầu',
            'EndDate.date_format'           =>     'Sai định dạng Ngày kết thúc',
            'EndDate.after_or_equal'        =>     'Ngày kết thúc phải sau Ngày bắt đầu',
            'NameProject.*.required'        =>     'Không được bỏ trống đầu mục công việc'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $errors->first(),
        ], 422));
    }
}
