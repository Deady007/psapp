<?php

namespace App\Http\Requests;

use App\Models\ProjectBoard;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreStoryRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'acceptance_criteria' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'assignee_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'due_date' => ['required', 'date'],
            'priority' => ['required', 'string', Rule::in(Story::PRIORITIES)],
            'labels' => ['nullable', 'string'],
            'estimate' => ['nullable', 'integer', 'min:0'],
            'estimate_unit' => ['nullable', 'string', Rule::in(['points', 'hours'])],
            'reference_links' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('assignee_id')) {
                return;
            }

            $assigneeId = $this->integer('assignee_id');

            if (! User::role('developer')->whereKey($assigneeId)->exists()) {
                $validator->errors()->add('assignee_id', 'Assigned user must have the developer role.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Story title is required.',
            'title.max' => 'Story title may not be greater than 255 characters.',
            'description.required' => 'Story description is required.',
            'description.string' => 'Story description must be a string.',
            'acceptance_criteria.string' => 'Acceptance criteria must be a string.',
            'notes.string' => 'Notes must be a string.',
            'assignee_id.required' => 'Please assign a developer.',
            'assignee_id.exists' => 'The selected assignee is invalid.',
            'due_date.required' => 'Due date is required.',
            'due_date.date' => 'Due date must be a valid date.',
            'priority.required' => 'Priority is required.',
            'priority.in' => 'The selected priority is invalid.',
            'labels.string' => 'Labels must be a string.',
            'estimate.integer' => 'Estimate must be a number.',
            'estimate.min' => 'Estimate must be at least 0.',
            'estimate_unit.in' => 'Estimate unit must be points or hours.',
            'reference_links.string' => 'Links must be a string.',
        ];
    }
}
