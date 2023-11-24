<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class UserStoreRequest extends BaseRequest
{
    /**
     * バリデーションルール
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['bail', 'required', 'string', 'email'],
            'password' => ['string'],
            'api_token' => ['required', 'string'],
        ];
    }

    /**
     * バリデーション後処理
     *
     * @return void
     */
    protected function passedValidation(): void
    {
        if (is_null($this->password)) {
            $this->merge([
                'password' => config('auth.default_password'),
            ]);
        }
    }
}
