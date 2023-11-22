<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreRegisterRequest extends FormRequest
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
            'name' => ['required', 'regex:/[一-龠ぁ-ゔァ-ヴーa-zA-Z0-9々〆〤ヶ]+/u'],
            'name_fr' => ['required', 'regex:/^[ァ-ヴー]+$/u'],
            'email' => ['required', 'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/i', 'unique:users'],
            'sex' => ['required'],
            'year' => ['required'],
            'month' => ['required'],
            'day' => ['required'],
            'phone_number' => ['required'],
            'city' => ['required'],
            'district' => ['required'],
            'password'=>['required', 'regex:/^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/'],
            'confirm_password'=>'required|same:password'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'お名前（は必須項目です/Vui lòng nhập nội dung trường tên',
            'name.regex' => 'Tên không đúng định dạng',
            'name_fr.regex' => 'Vui lòng nhập ký tự katagata',
            'name_fr.required' => 'フリガナは必須項目です/Vui lòng nhập nội dung trường furigana name',
            'email.required' => 'メールアドレスは必須項目です/Vui lòng nhập nội dung trường email',
            'email.unique'=>'The email address already exists in the system',
            'email.regex' => 'メールアドレの形式で入力してください',
            'sex.required' => '性別 は必須項目です/Vui lòng nhập nội dung trường giới tính',
            'year.required' => '生年月日は必須項目です/Vui lòng nhập nội dung trường ngày sinh',
            'month.required' => '生年月日は必須項目です/Vui lòng nhập nội dung trường ngày sinh',
            'day.required' => '生年月日は必須項目です/Vui lòng nhập nội dung trường ngày sinh',
            'phone_number.required' => '電話番号は必須項目です/Vui lòng nhập nội dung trường phone',
            'city.required' => '都道府県 は必須項目です/Vui lòng nhập nội dung trường thành phố',
            'district.required' => '市区町村は必須項目です/Vui lòng nhập nội dung trường quận',
            'password.required'=>'パスワードは必須項目です/Vui lòng nhập nội dung trường password',
            'password.regex'=>' パスワード の形式で入力してください',
            'confirm_password.required'=>'パスワードは必須項目です/Vui lòng nhập nội dung trường password',
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
