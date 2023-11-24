<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class ArticleIndexRequest extends BaseRequest
{
    /**
     * バリデーションルール
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'entry_ids' => ['array'],
        ];
    }
}
