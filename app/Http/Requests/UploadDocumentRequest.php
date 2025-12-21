<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadDocumentRequest extends FormRequest
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
        $project = $this->route('project');
        $folderRule = Rule::exists('document_folders', 'id');

        if ($project instanceof Project) {
            $folderRule = $folderRule->where(function ($query) use ($project) {
                $query->where('project_id', $project->id)
                    ->where('kind', '!=', 'trash');
            });
        }

        return [
            'folder_id' => ['nullable', 'integer', $folderRule],
            'file' => ['required', 'file', 'max:10240'],
            'source' => ['required', 'string', 'max:255'],
            'received_from' => ['required', 'string', 'max:255'],
            'received_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'folder_id.exists' => 'Selected folder is invalid.',
            'file.required' => 'Please upload a document.',
            'file.file' => 'The document must be a valid file.',
            'file.max' => 'The document may not be greater than 10 MB.',
            'source.required' => 'Source is required.',
            'received_from.required' => 'Received from is required.',
            'received_at.date' => 'Received at must be a valid date.',
        ];
    }
}
