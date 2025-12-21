<?php

namespace App\Http\Requests;

use App\Models\ProjectRequirement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequirementRequest extends FormRequest
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
            'module_name' => ['required', 'string', 'max:255'],
            'page_name' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'priority' => ['required', Rule::in(ProjectRequirement::PRIORITIES)],
            'status' => ['required', Rule::in(ProjectRequirement::STATUSES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'module_name.required' => 'Module name is required.',
            'module_name.max' => 'Module name may not be greater than 255 characters.',
            'page_name.max' => 'Page name may not be greater than 255 characters.',
            'title.required' => 'Requirement title is required.',
            'title.max' => 'Requirement title may not be greater than 255 characters.',
            'priority.required' => 'Please select a priority.',
            'priority.in' => 'The selected priority is invalid.',
            'status.required' => 'Please select a status.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
