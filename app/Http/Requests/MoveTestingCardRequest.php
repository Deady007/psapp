<?php

namespace App\Http\Requests;

use App\Models\ProjectBoard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoveTestingCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $board = $this->route('board');

        return $this->user() !== null
            && $board instanceof ProjectBoard
            && $board->isTesting();
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
        ];
    }
}
