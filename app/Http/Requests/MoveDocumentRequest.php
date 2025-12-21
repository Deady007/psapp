<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoveDocumentRequest extends FormRequest
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
            'destination_folder_id' => ['nullable', 'integer', $folderRule],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'destination_folder_id.exists' => 'Destination folder is invalid.',
        ];
    }
}
