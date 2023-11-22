<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CommentRequest extends FormRequest
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
                'body' => 'required|min:3|max:100',
            ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'お名前（は必須項目です/Vui lòng nhập nội dung comment',
            'body.min' => 'The article content must be at least 3 characters',
            'body.max' => 'The maximum article content is 100 characters',
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
