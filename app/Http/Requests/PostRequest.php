<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PostRequest extends FormRequest
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
            'body' => 'required|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'お名前（は必須項目です/Vui lòng nhập nội dung bài post',
            'body.min' => 'The article content must be at least 10 characters',
            'body.max' => 'The maximum article content is 1000 characters',
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
