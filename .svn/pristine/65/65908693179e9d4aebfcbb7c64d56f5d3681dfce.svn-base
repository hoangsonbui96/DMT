<?php

namespace App\Http\Requests;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EquipmentOfferRequest extends FormRequest
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
            'id'                => 'nullable|integer|min:1',
            'offer_user_id'     => 'required|integer|min:1',
            'offer_date'        => 'required|date_format:d/m/Y',
            'approved_user_id'  => 'nullable|integer|min:1',
            'content'           => 'required|string',
            'detail_id'         => 'array|min:1',
            'detail_id.*'       => 'integer|min:1',
            'description'       => 'required|array',
            'description.*'     => 'string',
            'quantity'          => 'required|array',
            'quantity.*'        => 'numeric|min:1',
            'unit_price'        => 'required|array',
            'unit_price.*'      => 'numeric|min:0',
//            'final_unit_price'  => 'array',
//            'final_unit_price.*'=> 'numeric|min:0',
            'price'             => 'array',
            'price.*'           => 'numeric|min:0',
            'buy_date'          => 'array',
            'buy_date.*'        => 'nullable|date_format:d/m/Y',
            'buy_user_id'       => 'array',
            'buy_user_id.*'     => 'nullable|integer|min:1',
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
            'detail_id.*.min' => 'id không hợp lệ.',
            'description.required' => 'Mô tả không được để trống.',
            'description.*.string' => 'Mô tả không được để trống.',
            'quantity.*.numeric' => 'Số lượng phải lớn hơn 0.',
            'quantity.*.min' => 'Số lượng phải lớn hơn 0.',
            'unit_price.*.numeric' => 'Đơn giá dự kiến phải lớn hơn hoặc bằng 0.',
            'unit_price.*.min' => 'Đơn giá dự kiến phải lớn hơn hoặc bằng 0.',
//            'final_unit_price.*.min' => 'Đơn giá chính thức phải lớn hơn hoặc bằng 0.',
//            'final_unit_price.*.numeric' => 'Đơn giá chính thức phải lớn hơn hoặc bằng 0.',
            'price.*.numeric' => 'Giá chính thức phải lớn hơn hoặc bằng 0.',
            'price.*.min' => 'Giá chính thức phải lớn hơn hoặc bằng 0.',
            'buy_date.*.date_format' => 'Ngày mua không hợp lệ.',
            'buy_user_id.*.min' => 'id không hợp lệ.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(AdminController::responseApi(422, $errors->first()));
    }
}
