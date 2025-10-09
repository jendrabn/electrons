<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThreadCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // authorization is handled in controllers via policies
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Use route name detection to return rules for store/update/delete.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Determine whether this is a reply (has a parent_id) or a top-level comment.
        // If parent_id is provided (and not empty), treat as reply and use a smaller max.
        $parentId = $this->input('parent_id');
        $isReply = ! empty($parentId);

        // Minimum length for any comment body. (Assumption: 3 characters)
        $min = 3;

        // Max length differs depending on whether it's a reply or a top-level comment.
        $max = $isReply ? 500 : 5000;

        $bodyRules = ['required', 'string', "min:$min", "max:$max"];

        if ($this->method() === 'POST') {
            return [
                'body' => $bodyRules,
                'parent_id' => ['nullable', 'exists:thread_comments,id'],
            ];
        } elseif ($this->method() === 'PUT') {
            return [
                'body' => $bodyRules,
            ];
        }

        return [];
    }
}
