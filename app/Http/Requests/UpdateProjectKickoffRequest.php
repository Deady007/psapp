<?php

namespace App\Http\Requests;

use App\Models\ProjectKickoff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectKickoffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'purchase_order_number' => ['nullable', 'string', 'max:255'],
            'scheduled_at' => ['nullable', 'date'],
            'meeting_mode' => ['nullable', 'string', 'max:255'],
            'stakeholders' => ['nullable', 'string'],
            'requirements_summary' => ['nullable', 'string'],
            'timeline_summary' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(ProjectKickoff::STATUSES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'purchase_order_number.max' => 'Purchase order number may not be greater than 255 characters.',
            'scheduled_at.date' => 'Kick-off date must be a valid date.',
            'meeting_mode.max' => 'Meeting mode may not be greater than 255 characters.',
            'status.required' => 'Please select a kick-off status.',
            'status.in' => 'The selected kick-off status is invalid.',
        ];
    }
}
