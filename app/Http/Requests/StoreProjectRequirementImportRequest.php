<?php

namespace App\Http\Requests;

use App\Models\ProjectRequirement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreProjectRequirementImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $requirements = $this->input('requirements', []);

        if (! is_array($requirements)) {
            return;
        }

        $filtered = collect($requirements)
            ->filter(fn ($item) => is_array($item) && array_key_exists('selected', $item))
            ->map(fn ($item) => Arr::except($item, ['selected']))
            ->values()
            ->all();

        $this->merge(['requirements' => $filtered]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'requirements' => ['required', 'array', 'min:1'],
            'requirements.*.module_name' => ['required', 'string', 'max:255'],
            'requirements.*.page_name' => ['nullable', 'string', 'max:255'],
            'requirements.*.title' => ['required', 'string', 'max:255'],
            'requirements.*.details' => ['nullable', 'string'],
            'requirements.*.priority' => ['required', Rule::in(ProjectRequirement::PRIORITIES)],
            'requirements.*.status' => ['required', Rule::in(ProjectRequirement::STATUSES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'requirements.required' => 'Please select at least one requirement to import.',
            'requirements.array' => 'Please select at least one requirement to import.',
            'requirements.min' => 'Please select at least one requirement to import.',
            'requirements.*.module_name.required' => 'Module name is required.',
            'requirements.*.module_name.max' => 'Module name may not be greater than 255 characters.',
            'requirements.*.page_name.max' => 'Page name may not be greater than 255 characters.',
            'requirements.*.title.required' => 'Requirement title is required.',
            'requirements.*.title.max' => 'Requirement title may not be greater than 255 characters.',
            'requirements.*.priority.required' => 'Please select a priority.',
            'requirements.*.priority.in' => 'The selected priority is invalid.',
            'requirements.*.status.required' => 'Please select a status.',
            'requirements.*.status.in' => 'The selected status is invalid.',
        ];
    }
}
