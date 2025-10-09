<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // authorization is handled via policies in the controller
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->method() === 'POST') {
            return [
                'body' => ['required', 'string', 'min:3', 'max:500'],
                'parent_id' => ['nullable', 'exists:post_comments,id'],
            ];
        }

        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            return [
                'body' => ['required', 'string', 'min:3', 'max:500'],
            ];
        }

        return [];
    }
}
