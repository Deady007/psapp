<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequirementTranscriptRequest extends FormRequest
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
            'transcript' => ['required', 'file', 'mimes:txt', 'mimetypes:text/plain', 'max:1024'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'transcript.required' => 'Please upload a transcript file.',
            'transcript.file' => 'The transcript must be a valid file.',
            'transcript.mimes' => 'The transcript must be a .txt file.',
            'transcript.mimetypes' => 'The transcript must be a plain text file.',
            'transcript.max' => 'The transcript may not be greater than 1 MB.',
        ];
    }
}
