<?php

namespace App\Http\Requests\WorkTask;

use App\Http\Controllers\Admin\AdminController;
use App\Project;
use App\WorkTask;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddErrorReviewRequest extends FormRequest
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
            'id' => 'required|integer',
            'Descriptions' => 'required|string|max:200',
            'StartDate' => 'required|date_format:d/m/Y',
            'EndDate' => 'nullable|date_format:d/m/Y||after_or_equal:StartDate',
            'Progressing' => 'required|numeric|between:0.1,100|lt:100',
            'Note' => 'nullable|string|max:200'
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Thiếu id',
            'Progressing.between' => 'Tiến độ phải trong khoảng từ 0% đến 100%',
            'Progressing.lt' => 'Tiến độ phải nhỏ hơn 100%',
            'StartDate.date_format' => 'Ngày bắt đầu sai định dạng',
            'EndDate.date_format' => 'Ngày kết thúc sai định dạng',
            'EndDate.after_or_equal' => 'Ngày kết thúc phải sau Ngày bắt đầu',
            'Descriptions.required' => 'Không được để trống mô tả',
            'Descriptions.max' => 'Mô tả không được vượt quá 200 ký tự',
            'Note.max' => 'Ghi chú không được vượt quá 200 ký tự'
        ];
    }

    public function withValidator($validator)
    {
        if (!$validator->fails()) {
            $project = WorkTask::find($this->id)->project;
            $start_date_task = Carbon::createFromFormat("d/m/Y", $this->StartDate)->format("Y-m-d");
            $validator->after(function ($validator) use ($project, $start_date_task) {
                if (is_null($this->EndDate)) {
                    if (is_null($project->EndDate)) {
                        if (Carbon::parse($start_date_task)->lt($project->StartDate)) {
                            $validator->errors()->add("StartDate", "Thời gian bắt đầu phải sau Ngày bắt đầu dự án");
                        }
                    } else {
                        if (Carbon::parse($start_date_task)->lt($project->StartDate)) {
                            $validator->errors()->add("StartDate", "Thời gian bắt đầu phải sau Ngày bắt đầu dự án");
                        }
                        if (Carbon::parse($start_date_task)->gt($project->EndDate)) {
                            $validator->errors()->add("StartDate", "Thời gian bắt đầu phải trước Ngày kết thúc dự án");
                        }
                    }
                } else {
                    $end_date_task = Carbon::createFromFormat("d/m/Y", $this->EndDate)->format("Y-m-d");
                    if (is_null($project->EndDate)) {
                        if (Carbon::parse($start_date_task)->lt($project->StartDate) || Carbon::parse($end_date_task)->lt($project->StartDate)) {
                            $validator->errors()->add("StartDate", "Thời gian bắt đầu hoặc kết thúc phải sau Ngày bắt đầu dự án");
                        }
                    } else {
                        if (Carbon::parse($start_date_task)->lt($project->StartDate) || Carbon::parse($start_date_task)->gt($project->EndDate)) {
                            $validator->errors()->add("StartDate", "Thời gian bắt đầu phải trong khoảng Ngày bắt đầu và Ngày kết thúc dự án");
                        }
                        if (Carbon::parse($end_date_task)->lt($project->StartDate) || Carbon::parse($end_date_task)->gt($project->EndDate)) {
                            $validator->errors()->add("EndDate", "Thời gian kết thúc phải trong khoảng Ngày bắt đầu và Ngày kết thúc dự án");
                        }
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
