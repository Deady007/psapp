<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('New Contact') }} — {{ $customer->name }}
            </h2>

            <a href="{{ route('customers.contacts.index', $customer) }}" class="text-sm text-gray-700 hover:text-gray-900">
                {{ __('Back to Contacts') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('customers.contacts.store', $customer) }}" class="space-y-6 js-ajax-contact-create">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="__('Phone')" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <div>
                            <x-input-label for="designation" :value="__('Designation')" />
                            <x-text-input id="designation" name="designation" type="text" class="mt-1 block w-full" :value="old('designation')" />
                            <x-input-error class="mt-2" :messages="$errors->get('designation')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                            <a href="{{ route('customers.contacts.index', $customer) }}" class="text-sm text-gray-700 hover:text-gray-900">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function () {
                $('.js-ajax-contact-create').on('submit', function (e) {
                    e.preventDefault();
                    const $form = $(this);
                    const submitBtn = $form.find('button[type="submit"]');
                    submitBtn.prop('disabled', true).addClass('opacity-70');

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
                            submitBtn.prop('disabled', false).removeClass('opacity-70');
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
