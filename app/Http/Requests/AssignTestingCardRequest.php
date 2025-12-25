<?php

namespace App\Http\Requests;

use App\Models\ProjectBoard;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AssignTestingCardRequest extends FormRequest
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
        return [
            'tester_id' => ['required', 'integer', Rule::exists('users', 'id')],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $testerId = $this->integer('tester_id');

            if ($testerId <= 0) {
                return;
            }

            if (! User::role('tester')->whereKey($testerId)->exists()) {
                $validator->errors()->add('tester_id', 'Assigned user must have the tester role.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tester_id.required' => 'Please select a tester.',
            'tester_id.exists' => 'The selected tester is invalid.',
        ];
    }
}
