<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Edit Customer') }}</h1>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Customer') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('customers.update', $customer) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', $customer->name)" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" :value="old('email', $customer->email)" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="form-group col-md-6">
                        <x-input-label for="phone" :value="__('Phone')" />
                        <x-text-input id="phone" name="phone" type="text" :value="old('phone', $customer->phone)" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <x-input-label for="address" :value="__('Address')" />
                        <textarea id="address" name="address" rows="2" class="form-control">{{ old('address', $customer->address) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div class="form-group col-md-6">
                        <x-input-label for="pincode" :value="__('Pincode')" />
                        <x-text-input id="pincode" name="pincode" type="text" :value="old('pincode', $customer->pincode)" />
                        <x-input-error class="mt-2" :messages="$errors->get('pincode')" />
                    </div>
                </div>

                <div class="form-group">
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" data-enhance="choices" class="form-control" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', $customer->status) === $status)>
                                {{ __($status) }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>

                <div class="form-group">
                    <x-input-label for="notes" :value="__('Notes')" />
                    <textarea id="notes" name="notes" rows="4" class="form-control">{{ old('notes', $customer->notes) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Update') }}</x-primary-button>
                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
