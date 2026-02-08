<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:sources,name,' . $this->source->id,
            'url' => 'required|url|unique:sources,url,' . $this->source->id,
            'rss_url' => 'nullable|url',
            'crawl_frequency' => 'nullable|integer|min:1|max:1440',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên nguồn tin là bắt buộc',
            'name.unique' => 'Tên nguồn tin đã tồn tại',
            'name.max' => 'Tên nguồn tin không được vượt quá 255 ký tự',
            'url.required' => 'URL của nguồn tin là bắt buộc',
            'url.url' => 'URL phải là một đường dẫn hợp lệ',
            'url.unique' => 'URL này đã được thêm',
            'rss_url.url' => 'RSS URL phải là một đường dẫn hợp lệ',
            'crawl_frequency.integer' => 'Tần suất crawl phải là số nguyên',
            'crawl_frequency.min' => 'Tần suất crawl phải ít nhất 1 phút',
        ];
    }
}
