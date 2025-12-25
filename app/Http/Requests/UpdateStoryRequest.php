<?php

namespace App\Http\Requests;

use App\Models\ProjectBoard;
use App\Models\Story;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $board = $this->route('board');

        return $this->user() !== null
            && $board instanceof ProjectBoard
            && $board->isDevelopment();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'acceptance_criteria' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['nullable', 'string', Rule::in(Story::PRIORITIES)],
            'labels' => ['nullable', 'string'],
            'estimate' => ['nullable', 'integer', 'min:0'],
            'estimate_unit' => ['nullable', 'string', Rule::in(['points', 'hours'])],
            'reference_links' => ['nullable', 'string'],
            'database_changes' => ['nullable', 'array'],
            'database_changes_confirmed' => ['nullable', 'boolean'],
            'page_mappings' => ['nullable', 'array'],
            'page_mappings_confirmed' => ['nullable', 'boolean'],
            'blocker_reason' => ['nullable', 'string', Rule::in(Story::BLOCKER_REASONS)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.max' => 'Story title may not be greater than 255 characters.',
            'description.string' => 'Story description must be a string.',
            'acceptance_criteria.string' => 'Acceptance criteria must be a string.',
            'notes.string' => 'Notes must be a string.',
            'due_date.date' => 'Due date must be a valid date.',
            'priority.in' => 'The selected priority is invalid.',
            'labels.string' => 'Labels must be a string.',
            'estimate.integer' => 'Estimate must be a number.',
            'estimate.min' => 'Estimate must be at least 0.',
            'estimate_unit.in' => 'Estimate unit must be points or hours.',
            'reference_links.string' => 'Links must be a string.',
            'database_changes.array' => 'Database changes must be a list.',
            'page_mappings.array' => 'Page mappings must be a list.',
            'blocker_reason.string' => 'Blocker reason must be a string.',
            'blocker_reason.in' => 'The selected blocker reason is invalid.',
        ];
    }
}
