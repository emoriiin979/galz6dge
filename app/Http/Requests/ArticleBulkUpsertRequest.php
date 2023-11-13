<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleBulkUpsertRequest extends FormRequest
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
            '*.entry_id' => ['required'],
            '*.title' => ['required'],
            '*.edited_at' => ['required', 'date_format:Y-m-d H:i:s'],
            '*.is_modified' => ['boolean'],
        ];
    }

    /**
     * 属性名
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];

        foreach ($this->input() as $key => $value) {
            $attributes[$key . '.entry_id'] = "entry_id";
            $attributes[$key . '.title'] = "title";
            $attributes[$key . '.edited_at'] = "edited_at";
            $attributes[$key . '.is_modified'] = "is_modified";
        }

        return $attributes;
    }
}
