<?php

namespace App\Http\Requests;

use App\Models\Bug;
use App\Models\ProjectBoard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBugRequest extends FormRequest
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
            'status' => ['nullable', 'string', Rule::in(Bug::STATUSES)],
            'severity' => ['nullable', 'string', Rule::in(Bug::SEVERITIES)],
            'assignee_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.in' => 'The selected status is invalid.',
            'severity.in' => 'The selected severity is invalid.',
            'assignee_id.exists' => 'The selected assignee is invalid.',
        ];
    }
}
