<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|min:10',
            'category_id' => 'required|exists:categories,id',
            'source_id' => 'nullable|exists:sources,id',
            'content' => 'required|string|min:100',
            'summary' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,webp,gif|max:5120',
            'image_url' => 'nullable|url',
            'author' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề bài viết là bắt buộc',
            'title.min' => 'Tiêu đề phải có ít nhất 10 ký tự',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'category_id.exists' => 'Danh mục được chọn không tồn tại',
            'content.required' => 'Nội dung bài viết là bắt buộc',
            'content.min' => 'Nội dung phải có ít nhất 100 ký tự',
            'image.image' => 'Tập phải là một hình ảnh',
            'image.mimes' => 'Hình ảnh phải là JPEG, PNG, WebP hoặc GIF',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 5MB',
            'image_url.url' => 'URL hình ảnh không hợp lệ',
            'author.max' => 'Tên tác giả không được vượt quá 255 ký tự',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->has('is_published'),
            'is_featured' => $this->has('is_featured'),
        ]);
    }
}
