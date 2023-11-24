<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    /**
     * リクエスト認可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 属性名
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];

        // アンダースコアの自動変換を防ぐための処置
        foreach ($this->rules() as $key => $value) {
            $attributes[$key] = $key;
        }

        return $attributes;
    }
}
