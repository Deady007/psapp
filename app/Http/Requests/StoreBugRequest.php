<?php

namespace App\Http\Requests;

use App\Models\Bug;
use App\Models\ProjectBoard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBugRequest extends FormRequest
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
        $board = $this->route('board');
        $testingCardRule = Rule::exists('testing_cards', 'id');

        if ($board instanceof ProjectBoard) {
            $testingCardRule = $testingCardRule->where('project_board_id', $board->id);
        }

        return [
            'testing_card_id' => ['required', 'integer', $testingCardRule],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'severity' => ['required', 'string', Rule::in(Bug::SEVERITIES)],
            'steps_to_reproduce' => ['nullable', 'string'],
            'assignee_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'testing_card_id.required' => 'Please select a testing card.',
            'testing_card_id.exists' => 'The selected testing card is invalid.',
            'title.required' => 'Bug title is required.',
            'title.max' => 'Bug title may not be greater than 255 characters.',
            'description.string' => 'Bug description must be a string.',
            'severity.required' => 'Bug severity is required.',
            'severity.in' => 'The selected bug severity is invalid.',
            'steps_to_reproduce.string' => 'Steps to reproduce must be a string.',
            'assignee_id.exists' => 'The selected assignee is invalid.',
        ];
    }
}
