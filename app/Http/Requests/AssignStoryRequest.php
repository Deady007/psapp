<?php

namespace App\Http\Requests;

use App\Models\ProjectBoard;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AssignStoryRequest extends FormRequest
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
            'assignee_id' => ['required', 'integer', Rule::exists('users', 'id')],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $assigneeId = $this->integer('assignee_id');

            if ($assigneeId <= 0) {
                return;
            }

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
            'assignee_id.required' => 'Please select a developer.',
            'assignee_id.exists' => 'The selected developer is invalid.',
        ];
    }
}
