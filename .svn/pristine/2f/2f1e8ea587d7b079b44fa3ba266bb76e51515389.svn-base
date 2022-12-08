<?php

namespace App\Http\Requests;

use App\Project;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class WorkTaskRequest extends FormRequest
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
            'id' => 'nullable|numeric',
            'ProjectID' => 'required|numeric',
            'Name' => 'required|array',
            'Name.*' => 'required|min:1|max:100',
            'StartDate' => 'array',
            'StartDate.*' => 'nullable|date_format:d/m/Y',
            'EndDate' => 'array',
            'EndDate.*' => 'nullable|date_format:d/m/Y',
            'Description' => 'array',
            'Description.*' => 'nullable|string',
            'Member' => 'array',
            'Member.*' => 'nullable|numeric',
            'Note' => 'array',
            'Note.*' => 'nullable|max:200',
            'Status' => 'array',
            'Status.*' => 'nullable|numeric',

        ];
    }

    public function messages()
    {
        return [
            'ProjectID.required' => 'Không được bỏ trống Tên dự án',
            'Name.*.required' => 'Không được bỏ trống Tên công việc',
            'Name.*.min' => 'Tên task phải có ít nhất 1 ký tự',
            'Name.*.max' => 'Tên task không được vượt quá 100 ký tự',
            'StartDate.*.date_format' => 'Sai định dạng Ngày bắt đầu',
            'EndDate.*.date_format' => 'Sai định dạng Ngày kết thúc',
            'EndDate.*.after_or_equal' => 'Ngày kết thúc phải sau Ngày bắt đầu',
            'Status.*.numeric' => 'Trạng thái không hợp lệ',
            'Note.array' => 'Ghi chú phải là một mảng',
            'Note.*.max' => 'Ghi chú không được vượt quá 200 ký tự'
        ];
    }

    public function withValidator($validator)
    {
        if (!$validator->fails()) {
            $project = Project::withTrashed()->where('id', $this->ProjectID)->first();
            $validator->after(function ($validator) use ($project) {
                if ($project->trashed()) {
                    $validator->errors()->add("ProjectID", "Dự án đã đóng, không thể tạo được task");
                    return null;
                }

                // Format date of project
                $start_date_project = $this->_formatDate("Y-m-d", $project->StartDate);
                $end_date_project = $this->_formatDate("Y-m-d", $project->EndDate);

                foreach ($this->Name as $i => $item) {

                    // Format date of task
                    $start_date_task = $this->_formatDate("d/m/Y", $this->StartDate[$i]);
                    $end_date_task = $this->_formatDate("d/m/Y", $this->EndDate[$i]);

                    // Task has start and end date
                    if ($start_date_task != null && $end_date_task != null) {

                        if ($start_date_task->gt($end_date_task)) {
                            $validator->errors()->add("StartDate", "Thời gian bắt đầu phải sau thời gian kết thúc");
                            break;
                        }
                        if ($start_date_task->lt($start_date_project) || $end_date_task->lt($start_date_project)) {
                            $validator->errors()->add("StartDate", "Thời gian bắt đầu hoặc kết thúc phải sau Ngày bắt đầu dự án");
                            break;
                        }
                        // Project has end date
                        if ($end_date_project != null) {
                            if (Carbon::now()->gt($end_date_project) && $this->id == null) {
                                $validator->errors()->add("EndDate", "Dự án đã kết thúc, không thể tạo được task");
                                break;
                            }
                            if ($start_date_task->lt($start_date_project) || $start_date_task->gt($end_date_project)) {
                                $validator->errors()->add("StartDate", "Thời gian bắt đầu phải trong khoảng Ngày bắt đầu và Ngày kết thúc dự án");
                                break;
                            }
                            if ($end_date_task->lt($start_date_project) || $end_date_task->gt($project->EndDate)) {
                                $validator->errors()->add("EndDate", "Thời gian kết thúc phải trong khoảng Ngày bắt đầu và Ngày kết thúc dự án");
                                break;
                            }
                        }
                    }

                    // Task has only start date
                    if ($start_date_task != null && $end_date_task == null) {

                        if ($start_date_task->lt($start_date_project)) {
                            $validator->errors()->add("StartDate", "Thời gian bắt đầu phải sau Ngày bắt đầu dự án");
                            break;
                        }
                        // Project has end date
                        if ($end_date_project != null) {
                            if (Carbon::now()->gt($end_date_project) && $this->id == null) {
                                $validator->errors()->add("EndDate", "Dự án đã kết thúc, không thể tạo được task");
                                break;
                            }
                            if ($start_date_task->lt($start_date_project)) {
                                $validator->errors()->add("StartDate", "Thời gian bắt đầu phải sau Ngày bắt đầu dự án");
                                break;
                            }
                            if ($start_date_task->gt($end_date_project)) {
                                $validator->errors()->add("StartDate", "Thời gian bắt đầu phải trước Ngày kết thúc dự án");
                                break;
                            }
                        }
                    }

                    // Task has only end date
                    if ($start_date_task == null && $end_date_task != null) {
                        $validator->errors()->add("Project", "Hãy nhập thêm thời gian bắt đầu nếu đã nhập thời gian kết thúc.");
                        break;
                    }
                }
            });
        }
    }

    private function _formatDate($string_format, $value)
    {
        return ($value == null || $value == "" || empty($value)) ? null : Carbon::createFromFormat($string_format, $value);
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
