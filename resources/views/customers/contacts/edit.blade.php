<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Edit Contact') }}</h1>
                <div class="text-muted">{{ $customer->name }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('customers.contacts.show', [$customer, $contact]) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Contact') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('customers.contacts.update', [$customer, $contact]) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', $contact->name)" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="form-group">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" :value="old('email', $contact->email)" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div class="form-group">
                    <x-input-label for="phone" :value="__('Phone')" />
                    <x-text-input id="phone" name="phone" type="text" :value="old('phone', $contact->phone)" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div class="form-group">
                    <x-input-label for="designation" :value="__('Designation')" />
                    <x-text-input id="designation" name="designation" type="text" :value="old('designation', $contact->designation)" />
                    <x-input-error class="mt-2" :messages="$errors->get('designation')" />
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Update') }}</x-primary-button>
                    <a href="{{ route('customers.contacts.show', [$customer, $contact]) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
