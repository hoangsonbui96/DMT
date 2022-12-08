<?php

namespace App\Http\Requests;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AbsenceRequest extends FormRequest
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
            'id'                   =>  'integer|min:1',
            'RoomID'               =>  'required|integer|min:1',
            'UID'                  =>  'required|integer|min:1',
            'MasterDataValue'      =>  'required|string',
            'SDate'                =>  'required|date_format:d/m/Y H:i',
            'EDate'                =>  'required|date_format:d/m/Y H:i',
            'Reason'               =>  'required|string',
            'Remark'               =>  'string|nullable',
            'RequestManager'       =>  'required|array',
            'AbsentDate'           =>  'nullable',
            'TotalTimeOff'         =>  'nullable|integer',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(AdminController::responseApi(422, $errors->first()));
    }
}