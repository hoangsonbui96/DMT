<?php

namespace App\Http\Requests\WorkTask;

use Illuminate\Foundation\Http\FormRequest;

class ReportErrorReviewRequest extends FormRequest
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
            'WorkingTime' => 'required|numeric|between:0.1,8',
            'Progressing' => 'required|numeric|between:0.1,100',
            'Contents' => 'required|string|max:200',
            'Note' => 'nullable|string|max:200',
            'ProjectID' => 'required|numeric|min:1',
            'TaskID' => 'required|numeric|min:1'
        ];
    }

    public function messages()
    {
        return [
            'WorkingTime.required' => 'Không được bỏ trống Thời gian làm',
            'WorkingTime.numeric' => 'Thời gian làm phải là số',
            'WorkingTime.between' => 'Thời gian làm lớn hơn 0 và nhỏ hơn hoặc bằng 8',
            'Contents.required' => 'Không được bỏ trống Nội dung',
            'Contents.max' => 'Nội dung không được vượt quá 200 ký tự',
            'Note.max' => 'Ghi chú không được vượt quá 200 ký tự'
        ];
    }
}
