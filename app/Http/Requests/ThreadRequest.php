<?php

namespace App\Http\Requests;

use App\Models\Thread;
use Illuminate\Foundation\Http\FormRequest;

class ThreadRequest extends FormRequest
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
        switch ($this->method()) {
            case 'POST':
                return [
                    'title' => ['required', 'string', 'max:200'],
                    'slug' => ['required', 'string', 'max:255', 'unique:threads,slug'],
                    'body' => ['required', 'string'],
                    'category_ids' => ['required', 'array'],
                    'category_ids.*' => ['required', 'numeric', 'exists:categories,id'],
                ];
                break;

            case 'PUT':
                return [
                    'title' => ['required', 'string', 'max:200'],
                    'slug' => ['required', 'string', 'max:255', 'unique:threads,slug,' . $this->thread->id],
                    'body' => ['required', 'string'],
                    'category_ids' => ['required', 'array'],
                    'category_ids.*' => ['required', 'numeric', 'exists:categories,id'],
                ];
                break;

            default:
                break;
        }


        return [
            //
        ];
    }


    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->uniqueSlug($this->title),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        if ($this->method() === 'POST') {
            $this->merge([
                'user_id' => auth()->user()->id,
            ]);
        }
    }

    private function uniqueSlug(string $title): string
    {
        do {
            $slug = str()->slug($title . '-' . str()->random(7));
        } while (Thread::where('slug', $slug)->exists());

        return $slug;
    }
}
