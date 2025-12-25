<?php

namespace App\Http\Requests;

use App\Models\BoardDocument;
use App\Models\ProjectBoard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBoardDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $board = $this->route('board');

        return $this->user() !== null
            && $board instanceof ProjectBoard
            && ($board->isDevelopment() || $board->isTesting());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $board = $this->route('board');
        $allowedTypes = BoardDocument::TYPES;

        if ($board instanceof ProjectBoard) {
            $allowedTypes = $board->isDevelopment()
                ? [BoardDocument::TYPE_USER_MANUAL]
                : [BoardDocument::TYPE_VALIDATION_REPORT];
        }

        return [
            'type' => ['required', 'string', Rule::in($allowedTypes)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Document type is required.',
            'type.in' => 'The selected document type is invalid.',
        ];
    }
}
