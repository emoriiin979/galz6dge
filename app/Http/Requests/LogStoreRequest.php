<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class LogStoreRequest extends BaseRequest
{
    /**
     * バリデーションルール
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'method' => ['bail', 'required', 'string', 'in:GET,POST,PUT,PATCH,DELETE'],
            'url' => ['bail', 'required', 'string', 'url'],
            'key' => ['bail', 'required', 'string'],
            'response_code' => ['bail', 'required', 'integer'],
            'message' => ['bail', 'required', 'string'],
        ];
    }

    /**
     * カスタムメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'method.in' => 'methodには正しいHTTPメソッドを指定してください。(例:POST)',
        ];
    }

    /**
     * バリデーション前処理
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->method)) {
            $this->merge([
                'method' => strtoupper($this->method),
            ]);
        }
    }
}
