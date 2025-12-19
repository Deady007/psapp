<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('New Project') }}
            </h2>

            <a href="{{ route('projects.index') }}" class="text-sm text-gray-700 hover:text-gray-900">
                {{ __('Back to Projects') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('projects.store') }}" class="space-y-6 js-ajax-project-create">
                        @csrf

                        <div>
                            <x-input-label for="customer_id" :value="__('Customer')" />
                            <select
                                id="customer_id"
                                name="customer_id"
                                data-enhance="choices"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">{{ __('Select a customer') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected((int) old('customer_id') === $customer->id)>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('customer_id')" />
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="code" :value="__('Code')" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code')" />
                            <x-input-error class="mt-2" :messages="$errors->get('code')" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select
                                id="status"
                                name="status"
                                data-enhance="choices"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(old('status', 'draft') === $status)>
                                        {{ __($status) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="start_date" :value="__('Start Date')" />
                                <x-text-input id="start_date" name="start_date" type="text" data-datepicker="1" class="mt-1 block w-full" :value="old('start_date')" />
                                <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                            </div>

                            <div>
                                <x-input-label for="due_date" :value="__('Due Date')" />
                                <x-text-input id="due_date" name="due_date" type="text" data-datepicker="1" class="mt-1 block w-full" :value="old('due_date')" />
                                <x-input-error class="mt-2" :messages="$errors->get('due_date')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                            <a href="{{ route('projects.index') }}" class="text-sm text-gray-700 hover:text-gray-900">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function () {
                $('.js-ajax-project-create').on('submit', function (e) {
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
                            const target = resp.redirect || "{{ route('projects.index') }}";
                            window.location.href = target;
                        },
                        error: function (xhr) {
                            submitBtn.prop('disabled', false).removeClass('opacity-70');
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                                alert(firstError);
                            } else {
                                alert('Unable to save project right now. Please try again.');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
