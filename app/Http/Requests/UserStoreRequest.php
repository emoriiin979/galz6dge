<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['string'],
            'api_token' => ['required', 'string'],
        ];
    }

    /**
     * 属性名
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'api_token' => 'api_token',
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
