<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class LogIndexRequest extends BaseRequest
{
    /**
     * バリデーションルール
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'methods' => ['array'],
            'methods.*' => ['bail', 'string', 'in:GET,POST,PUT,PATCH,DELETE'],
            'url' => ['string'],
            'from' => ['date_format:Y-m-d'],
            'to' => ['date_format:Y-m-d'],
        ];
    }

    /**
     * 属性名
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = parent::attributes();

        $attributes['methods.*'] = 'methodsの要素';

        return $attributes;
    }

    /**
     * カスタムメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'methods.*.in' => 'methodsの要素には正しいHTTPメソッドを指定してください。(例:POST)',
        ];
    }

    /**
     * バリデーション後処理
     *
     * @return void
     */
    protected function passedValidation(): void
    {
        if (!is_null($this->from)) {
            $this->merge(['from' => $this->from . ' 00:00:00']);
        }
        if (!is_null($this->to)) {
            $this->merge(['to' => $this->to . ' 23:59:59']);
        }
    }
}
