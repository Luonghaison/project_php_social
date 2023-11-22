<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'old_password'=>'required',
            'password'=>['required', 'regex:/^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/'],
            'confirm_password'=>'required|same:password'
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required'=>' パスワードは必須項目です/Vui lòng nhập nội dung trường old password',
            'password.required'=>' パスワードは必須項目です/Vui lòng nhập nội dung trường old password',
            'password.regex'=>' パスワード の形式で入力してください',
            'confirm_password.required'=>'以前のパスワードは必須項目です/Vui lòng nhập nội dung trường password',
            'confirm_password.same'=>'The confirmation password must match the password'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = new \Illuminate\Http\Response([
            'errors'=>$validator->errors(),
        ], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        throw (new ValidationException($validator, $response));
    }
}
