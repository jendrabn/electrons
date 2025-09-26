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
        if ($this->routeIs('comunity.store')) {
            return [
                'title' => ['required', 'string', 'max:200'],
                'user_id' => ['required', 'exists:users,id'],
                'slug' => ['required', 'string', 'max:255', 'unique:threads,slug'],
                'body' => ['required', 'string'],
                'tag_ids' => ['required', 'array'],
                'tag_ids.*' => ['required', 'numeric', 'exists:tags,id'],
            ];
        } else if ($this->routeIs('comunity.update')) {
            return [
                'title' => ['required', 'string', 'max:200'],
                'slug' => ['required', 'string', 'max:255', 'unique:threads,slug,' . $this->thread->id],
                'body' => ['required', 'string'],
                'tag_ids' => ['required', 'array'],
                'tag_ids.*' => ['required', 'numeric', 'exists:tags,id'],
            ];
        } else {
            return [];
        }
    }


    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        $data['slug'] = $this->uniqueSlug($this->title);

        if ($this->routeIs('comunity.store')) {
            $data['user_id'] = auth()->id();
        }

        if (!empty($data)) {
            $this->merge($data);
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
