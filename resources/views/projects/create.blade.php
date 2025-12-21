<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('New Project') }}</h1>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                    {{ __('Back to Projects') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('projects.store') }}" class="js-ajax-project-create">
                @csrf

                <div class="form-group">
                    <x-input-label for="customer_id" :value="__('Customer')" />
                    <select id="customer_id" name="customer_id" data-enhance="choices" class="form-control" required>
                        <option value="">{{ __('Select a customer') }}</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((int) old('customer_id') === $customer->id)>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('customer_id')" />
                </div>

                @php($selectedProducts = collect(old('products', []))->map(fn ($value) => (int) $value)->all())
                <div class="form-group">
                    <x-input-label for="products" :value="__('Products')" />
                    <select id="products" name="products[]" data-enhance="choices" class="form-control" multiple>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(in_array($product->id, $selectedProducts, true))>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('products')" />
                    <x-input-error class="mt-2" :messages="$errors->get('products.*')" />
                </div>

                <div class="form-group">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="form-group">
                    <x-input-label for="code" :value="__('Code')" />
                    <x-text-input id="code" name="code" type="text" :value="old('code')" />
                    <x-input-error class="mt-2" :messages="$errors->get('code')" />
                </div>

                <div class="form-group">
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" data-enhance="choices" class="form-control" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', 'draft') === $status)>
                                {{ __($status) }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <x-input-label for="start_date" :value="__('Start Date')" />
                        <x-text-input id="start_date" name="start_date" type="text" data-datepicker="1" :value="old('start_date')" />
                        <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                    </div>

                    <div class="form-group col-md-6">
                        <x-input-label for="due_date" :value="__('Due Date')" />
                        <x-text-input id="due_date" name="due_date" type="text" data-datepicker="1" :value="old('due_date')" />
                        <x-input-error class="mt-2" :messages="$errors->get('due_date')" />
                    </div>
                </div>

                <div class="form-group">
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Save') }}</x-primary-button>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            $(function () {
                $('.js-ajax-project-create').on('submit', function (e) {
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
                            const target = resp.redirect || "{{ route('projects.index') }}";
                            window.location.href = target;
                        },
                        error: function (xhr) {
                            submitBtn.prop('disabled', false);
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
