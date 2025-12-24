<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('New Contact') }}</h1>
                <div class="text-muted">{{ $customer->name }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('customers.contacts.index', $customer) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Contacts') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('customers.contacts.store', $customer) }}" class="js-ajax-contact-create">
                @csrf

                <div class="form-group">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="form-group">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" :value="old('email')" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div class="form-group">
                    <x-input-label for="phone" :value="__('Phone')" />
                    <x-text-input id="phone" name="phone" type="text" :value="old('phone')" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div class="form-group">
                    <x-input-label for="designation" :value="__('Designation')" />
                    <x-text-input id="designation" name="designation" type="text" :value="old('designation')" />
                    <x-input-error class="mt-2" :messages="$errors->get('designation')" />
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Save') }}</x-primary-button>
                    <a href="{{ route('customers.contacts.index', $customer) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            $(function () {
                $('.js-ajax-contact-create').on('submit', function (e) {
                    e.preventDefault();
                    const $form = $(this);
                    const submitBtn = $form.find('button[type="submit"]');
                    submitBtn.prop('disabled', true);

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: $form.serialize(),
                        headers: {
                            'Accept': 'application/json',
                        },
                        success: function (resp) {
                            const target = resp.redirect || "{{ route('customers.show', $customer) }}";
                            window.location.href = target;
                        },
                        error: function (xhr) {
                            submitBtn.prop('disabled', false);
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                                alert(firstError);
                            } else {
                                alert('Unable to save contact right now. Please try again.');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
