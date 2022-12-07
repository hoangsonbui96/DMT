<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCreateUser extends FormRequest
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
            'username'  =>  'required|alpha_num|min:3|max:15|unique:users',
            'email' =>  'required|not_contains|string|email|unique:users|confirmed',
            'password' => 'required|string|min:6|confirmed',

        ];
    }

    public function messages()
    {

    }
    public function attributes()
    {

    }


}
