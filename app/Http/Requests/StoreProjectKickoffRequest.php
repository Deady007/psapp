<?php

namespace App\Http\Requests;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectKickoffRequest extends FormRequest
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
            'notes' => ['nullable', 'string'],
            'stakeholders' => ['nullable', 'array'],
            'stakeholders.*' => [
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || ! str_contains($value, ':')) {
                        $fail('The selected stakeholder is invalid.');

                        return;
                    }

                    [$type, $id] = explode(':', $value, 2);
                    $map = [
                        'customer' => Customer::class,
                        'contact' => Contact::class,
                        'user' => User::class,
                    ];

                    if (! array_key_exists($type, $map) || ! ctype_digit($id)) {
                        $fail('The selected stakeholder is invalid.');

                        return;
                    }

                    if (! $map[$type]::query()->whereKey((int) $id)->exists()) {
                        $fail('The selected stakeholder is invalid.');
                    }
                },
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'purchase_order_number.max' => 'Purchase order number may not be greater than 255 characters.',
            'stakeholders.array' => 'Stakeholders must be an array.',
        ];
    }
}
