<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            'title.required' => 'Tiêu d? bài vi?t là b?t bu?c',
            'title.min' => 'Tiêu d? ph?i có ít nh?t 10 kı t?',
            'title.max' => 'Tiêu d? không du?c vu?t quá 255 kı t?',
            'category_id.required' => 'Vui lòng ch?n danh m?c',
            'category_id.exists' => 'Danh m?c du?c ch?n không t?n t?i',
            'content.required' => 'N?i dung bài vi?t là b?t bu?c',
            'content.min' => 'N?i dung ph?i có ít nh?t 100 kı t?',
            'image.image' => 'T?p ph?i là m?t hình ?nh',
            'image.mimes' => 'Hình ?nh ph?i là JPEG, PNG, WebP ho?c GIF',
            'image.max' => 'Kích thu?c hình ?nh không du?c vu?t quá 5MB',
            'image_url.url' => 'URL hình ?nh không h?p l?',
            'author.max' => 'Tên tác gi? không du?c vu?t quá 255 kı t?',
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
