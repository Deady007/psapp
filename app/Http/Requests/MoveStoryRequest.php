<?php

namespace App\Http\Requests;

use App\Models\ProjectBoard;
use App\Models\Story;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoveStoryRequest extends FormRequest
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
        $board = $this->route('board');
        $columnRule = Rule::exists('project_board_columns', 'id');

        if ($board instanceof ProjectBoard) {
            $columnRule = $columnRule->where('project_board_id', $board->id);
        }

        return [
            'column_id' => ['required', 'integer', $columnRule],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'blocker_reason' => ['nullable', 'string', Rule::in(Story::BLOCKER_REASONS)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'column_id.required' => 'Please select a column.',
            'column_id.exists' => 'The selected column is invalid.',
            'reason.string' => 'Reason must be a string.',
            'reason.max' => 'Reason may not be greater than 255 characters.',
            'notes.string' => 'Notes must be a string.',
            'blocker_reason.string' => 'Blocker reason must be a string.',
            'blocker_reason.in' => 'The selected blocker reason is invalid.',
        ];
    }
}
