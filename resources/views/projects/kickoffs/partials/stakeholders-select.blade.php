@php($selectedStakeholders = $selectedStakeholders ?? [])

<div class="form-group">
    <x-input-label for="stakeholders" :value="__('Stakeholders (@)')" />
    <select id="stakeholders" name="stakeholders[]" data-control="select2" class="form-control" multiple>
        @if (($stakeholderOptions['customers'] ?? collect())->isNotEmpty())
            <optgroup label="{{ __('Customers') }}">
                @foreach ($stakeholderOptions['customers'] as $customer)
                    @php($token = 'customer:'.$customer->id)
                    <option value="{{ $token }}" @selected(in_array($token, $selectedStakeholders, true))>
                        &#64;{{ $customer->name }}
                    </option>
                @endforeach
            </optgroup>
        @endif

        @if (($stakeholderOptions['contacts'] ?? collect())->isNotEmpty())
            <optgroup label="{{ __('Contacts') }}">
                @foreach ($stakeholderOptions['contacts'] as $contact)
                    @php($token = 'contact:'.$contact->id)
                    <option value="{{ $token }}" @selected(in_array($token, $selectedStakeholders, true))>
                        &#64;{{ $contact->name }}
                    </option>
                @endforeach
            </optgroup>
        @endif

        @if (($stakeholderOptions['users'] ?? collect())->isNotEmpty())
            <optgroup label="{{ __('Users') }}">
                @foreach ($stakeholderOptions['users'] as $user)
                    @php($token = 'user:'.$user->id)
                    <option value="{{ $token }}" @selected(in_array($token, $selectedStakeholders, true))>
                        &#64;{{ $user->name }}
                    </option>
                @endforeach
            </optgroup>
        @endif
    </select>
    <small class="text-muted">{{ __('Type @ to filter stakeholders.') }}</small>
    <x-input-error class="mt-2" :messages="$errors->get('stakeholders')" />
    <x-input-error class="mt-2" :messages="$errors->get('stakeholders.*')" />
</div>
