<?php

namespace App\Http\Requests;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProjectMngtRequest extends FormRequest
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
            'id'                            =>    'nullable|min:1',
            'MeetingName'                   =>    'required|string',
            'ProjectID'                     =>    'nullable|integer',
            'AssignID'                      =>    'required|array',
            'AssignID.*'                    =>    'required|integer',
            'MeetingTimeFrom'               =>    'required|date_format:d/m/Y',
            'MeetingTimeTo'                 =>    'nullable|date_format:d/m/Y',
            'TimeEnd'                       =>    'nullable|date_format:d/m/Y H:i',
            'ChairID'                       =>    'required|integer|min:1',
            'isPrivate'                     =>    'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'MeetingName.required'          =>  'Tiêu đề không được để trống.',
            'MeetingTimeFrom.required'      =>  'Thời gian bắt đầu không được để trống.',
            'MeetingTimeFrom.date_format'   =>  'Thời gian bắt đầu không hợp lệ.',
            'MeetingTimeTo.date_format'     =>  'Thời gian kết thúc không hợp lệ.',
            'TimeEnd.date_format'           =>  'Hạn nộp báo cáo không hợp lệ',
            'ChairID.required'              =>  'Chưa chọn người nhận báo cáo.',
            'AssignID.required'             =>  'Chưa chọn người tham gia.',
            'AssignID.*.required'           =>  'Chưa chọn người tham gia.',
            'AssignID.*.integer'            =>  'Người tham gia không hợp lệ.',
        ];
    }

    public function withValidator($validator){
        if (!$validator->fails()) {
            $_from = Carbon::createFromFormat("d/m/Y", $this->MeetingTimeFrom);
            $_to = $this->MeetingTimeTo != null ? Carbon::createFromFormat("d/m/Y", $this->MeetingTimeTo) : null;
            $validator->after(function ($validator) use ($_from, $_to) {
                if ($_to != null && $_from->gt($_to)) {
                    $validator->errors()->add("MeetingTimeTo", "Thời gian kết thúc không hợp lệ");
                }
            });
        }
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
