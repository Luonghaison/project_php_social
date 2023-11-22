<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UserLoginRequest extends FormRequest
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
            'email'=>'required|max:255','regex:/^[a-z0-9]+@[a-z]+\.[a-z]{2,3}$/i',
            'password'=>'required|max:32'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'ERR_COM_0001：メールアドレスは必須項目です',
            'email.max' => 'ERR_COM_0017：メールアドレスを:max文字以内で入力してください',
            'パスワード.required' => 'ERR_COM_0001：メールアドレスは必須項目です',
            'パスワード.max' => 'ERR_COM_0017：メールアドレスを:max文字以内で入力してください',
            'email.regex' => 'ERR_US101_0001：メールアドレスもしくはパスワードが不正です'
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
