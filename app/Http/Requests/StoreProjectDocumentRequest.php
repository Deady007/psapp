<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'],
            'notes' => ['nullable', 'string'],
            'collected_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.required' => 'Document category is required.',
            'category.max' => 'Document category may not be greater than 255 characters.',
            'file.required' => 'Please upload a document.',
            'file.file' => 'The document must be a valid file.',
            'file.max' => 'The document may not be greater than 10 MB.',
            'collected_at.date' => 'Collected at must be a valid date.',
        ];
    }
}
