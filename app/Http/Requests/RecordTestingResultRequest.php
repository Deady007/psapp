<?php

namespace App\Http\Requests;

use App\Models\Bug;
use App\Models\ProjectBoard;
use App\Models\TestingCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordTestingResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $board = $this->route('board');
        $user = $this->user();

        return $user !== null
            && $board instanceof ProjectBoard
            && $board->isTesting()
            && $user->hasAnyRole(['tester', 'admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'result' => ['required', 'string', Rule::in(TestingCard::RESULTS)],
            'notes' => ['nullable', 'string'],
            'bug_title' => ['required_if:result,'.TestingCard::RESULT_FAIL, 'string', 'max:255'],
            'bug_severity' => ['required_if:result,'.TestingCard::RESULT_FAIL, 'string', Rule::in(Bug::SEVERITIES)],
            'bug_description' => ['nullable', 'string'],
            'bug_steps' => ['nullable', 'string'],
            'bug_assignee_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'result.required' => 'Please select a test result.',
            'result.in' => 'The selected test result is invalid.',
            'notes.string' => 'Notes must be a string.',
            'bug_title.required_if' => 'Bug title is required when a test fails.',
            'bug_title.max' => 'Bug title may not be greater than 255 characters.',
            'bug_severity.required_if' => 'Bug severity is required when a test fails.',
            'bug_severity.in' => 'The selected bug severity is invalid.',
            'bug_description.string' => 'Bug description must be a string.',
            'bug_steps.string' => 'Bug steps must be a string.',
            'bug_assignee_id.exists' => 'The selected bug assignee is invalid.',
        ];
    }
}
