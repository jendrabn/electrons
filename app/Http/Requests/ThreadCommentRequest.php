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
        // store: community comments.store
        if ($this->routeIs('comunity.comments.store')) {
            return [
                'body' => ['required', 'string'],
                'parent_id' => ['nullable', 'exists:thread_comments,id'],
            ];
        } else if ($this->routeIs('comunity.comments.update')) {
            return [
                'body' => ['required', 'string'],
            ];
        } else {
            return [];
        }
    }
}
