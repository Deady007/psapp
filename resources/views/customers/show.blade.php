<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $customer->name }}
            </h2>

            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    id="customer-edit-toggle"
                    data-mode="view"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >Edit</button>

                <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('{{ __('Delete this customer?') }}')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>{{ __('Delete') }}</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 px-6 pt-6">
                    <div class="flex flex-wrap gap-3">
                        <span class="px-4 py-2 text-sm font-medium rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {{ __('Details') }}
                        </span>
                        <a
                            href="#contacts-section"
                            class="px-4 py-2 text-sm font-medium rounded-md text-gray-700 border border-gray-200 hover:bg-gray-50"
                        >
                            {{ __('Contacts') }} ({{ $customer->contacts_count }})
                        </a>
                        <a
                            href="{{ route('projects.index', ['customer_id' => $customer->id]) }}"
                            class="px-4 py-2 text-sm font-medium rounded-md text-gray-700 border border-gray-200 hover:bg-gray-50"
                        >
                            {{ __('Projects') }} ({{ $customer->projects_count }})
                        </a>
                    </div>
                </div>

                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('Customer Details') }}</h3>
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                class="text-sm text-gray-600 hover:text-gray-800 hidden"
                                id="customer-cancel-edit"
                            >
                                {{ __('Cancel') }}
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" id="customer-view-section">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Email') }}</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $customer->email ?: '—' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Phone') }}</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $customer->phone ?: '—' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Address') }}</h3>
                            <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $customer->address ?: '—' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Pincode') }}</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $customer->pincode ?: '—' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Status') }}</h3>
                            <div class="mt-1">
                                @if ($customer->status === 'active')
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                        {{ __('active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">
                                        {{ __('inactive') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">{{ __('Notes') }}</h3>
                            <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                                {{ $customer->notes ?: __('—') }}
                            </p>
                        </div>
                    </div>

                    <div id="customer-edit-section" class="hidden">
                        <form id="customer-inline-form" class="grid grid-cols-1 sm:grid-cols-2 gap-6 js-ajax-customer-update" method="POST" action="{{ route('customers.update', $customer) }}">
                            @csrf
                            @method('PUT')
                            <div class="sm:col-span-2">
                                <x-input-label for="edit_name" :value="__('Name')" />
                                <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full" value="{{ $customer->name }}" required />
                            </div>
                            <div>
                                <x-input-label for="edit_email" :value="__('Email')" />
                                <x-text-input id="edit_email" name="email" type="email" class="mt-1 block w-full" value="{{ $customer->email }}" />
                            </div>
                            <div>
                                <x-input-label for="edit_phone" :value="__('Phone')" />
                                <x-text-input id="edit_phone" name="phone" type="text" class="mt-1 block w-full" value="{{ $customer->phone }}" />
                            </div>
                            <div>
                                <x-input-label for="edit_address" :value="__('Address')" />
                                <textarea id="edit_address" name="address" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ $customer->address }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="edit_pincode" :value="__('Pincode')" />
                                <x-text-input id="edit_pincode" name="pincode" type="text" class="mt-1 block w-full" value="{{ $customer->pincode }}" />
                            </div>
                            <div>
                                <x-input-label for="edit_status" :value="__('Status')" />
                                <select
                                    id="edit_status"
                                    name="status"
                                    data-enhance="choices"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}" @selected($customer->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="edit_notes" :value="__('Notes')" />
                                <textarea id="edit_notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ $customer->notes }}</textarea>
                            </div>
                            <div class="sm:col-span-2 flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                                <button type="button" class="text-sm text-gray-700 hover:text-gray-900" id="customer-cancel-edit-inline">{{ __('Cancel') }}</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="p-6 text-gray-900" id="contacts-section">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('Contacts') }}</h3>
                        <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 js-open-contact-modal"
                            data-mode="create"
                            data-action="{{ route('customers.contacts.store', $customer) }}"
                        >
                            {{ __('Add Contact') }}
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Email') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Phone') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Designation') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($customer->contacts as $contact)
                                    <tr data-row-id="contact-{{ $contact->id }}">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $contact->name }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $contact->email ?: '—' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $contact->phone ?: '—' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $contact->designation ?: '—' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                            <button
                                                type="button"
                                                class="text-gray-700 hover:text-gray-900 js-open-contact-modal"
                                                data-mode="edit"
                                                data-action="{{ route('customers.contacts.update', [$customer, $contact]) }}"
                                                data-name="{{ $contact->name }}"
                                                data-email="{{ $contact->email }}"
                                                data-phone="{{ $contact->phone }}"
                                                data-designation="{{ $contact->designation }}"
                                            >
                                                {{ __('Edit') }}
                                            </button>

                                            <form method="POST" action="{{ route('customers.contacts.destroy', [$customer, $contact]) }}" class="inline js-ajax-delete" data-row="contact-{{ $contact->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-sm text-gray-600">{{ __('No contacts yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('customers.index') }}" class="text-sm text-gray-700 hover:text-gray-900">
                    {{ __('Back to Customers') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contact-modal" class="fixed inset-0 bg-gray-900/50 hidden z-50 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl">
            <div class="bg-white rounded-lg shadow-xl w-full">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800" id="contact-modal-title">{{ __('Add Contact') }}</h3>
                    <button type="button" class="text-gray-500 hover:text-gray-700 js-close-contact-modal">&times;</button>
                </div>
                <div class="p-6">
                    <form id="contact-modal-form" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @csrf
                        <input type="hidden" name="_method" value="POST">

                        <div>
                            <x-input-label for="modal_contact_name" :value="__('Name')" />
                            <x-text-input id="modal_contact_name" name="name" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="modal_contact_email" :value="__('Email')" />
                            <x-text-input id="modal_contact_email" name="email" type="email" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="modal_contact_phone" :value="__('Phone')" />
                            <x-text-input id="modal_contact_phone" name="phone" type="text" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="modal_contact_designation" :value="__('Designation')" />
                            <x-text-input id="modal_contact_designation" name="designation" type="text" class="mt-1 block w-full" />
                        </div>

                        <div class="sm:col-span-2 flex items-center justify-end gap-3 pt-2">
                            <button type="button" class="text-sm text-gray-700 hover:text-gray-900 js-close-contact-modal">{{ __('Cancel') }}</button>
                            <x-primary-button id="contact-modal-submit">{{ __('Save') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function () {
                const $view = $('#customer-view-section');
                const $edit = $('#customer-edit-section');
                const $editBtn = $('#customer-edit-toggle');
                const $cancelBtn = $('#customer-cancel-edit');
                const $cancelInline = $('#customer-cancel-edit-inline');
                const $form = $('#customer-inline-form');

                function enterEdit() {
                    $view.addClass('hidden');
                    $edit.removeClass('hidden');
                    $editBtn.text('Save').data('mode', 'edit');
                    $cancelBtn.removeClass('hidden');
                    $cancelInline.removeClass('hidden');
                    $('#edit_name').focus();
                }

                function exitEdit() {
                    $view.removeClass('hidden');
                    $edit.addClass('hidden');
                    $editBtn.text('Edit').data('mode', 'view');
                    $cancelBtn.addClass('hidden');
                    $cancelInline.addClass('hidden');
                }

                $editBtn.on('click', function () {
                    if ($editBtn.data('mode') === 'view') {
                        enterEdit();
                    } else {
                        $form.trigger('submit');
                    }
                });

                $cancelBtn.on('click', exitEdit);
                $cancelInline.on('click', exitEdit);

                $('.js-ajax-delete').on('submit', function (e) {
                    e.preventDefault();
                    const $formDel = $(this);
                    const targetRow = $formDel.data('row');
                    if (!confirm('{{ __('Delete this contact?') }}')) {
                        return;
                    }

                    $.ajax({
                        url: $formDel.attr('action'),
                        method: 'POST',
                        data: $formDel.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
                        },
                        success: function () {
                            $('[data-row-id=\"' + targetRow + '\"]').fadeOut(200, function () {
                                $(this).remove();
                            });
                        },
                        error: function () {
                            alert('Unable to delete contact right now. Please try again.');
                        }
                    });
                });

                const $modal = $('#contact-modal');
                const $modalForm = $('#contact-modal-form');
                const $methodInput = $modalForm.find('input[name=\"_method\"]');
                const $title = $('#contact-modal-title');

                function openModal(mode, action, data = {}) {
                    $modalForm.attr('action', action);
                    $methodInput.val(mode === 'edit' ? 'PUT' : 'POST');
                    $title.text(mode === 'edit' ? '{{ __('Edit Contact') }}' : '{{ __('Add Contact') }}');

                    $('#modal_contact_name').val(data.name || '');
                    $('#modal_contact_email').val(data.email || '');
                    $('#modal_contact_phone').val(data.phone || '');
                    $('#modal_contact_designation').val(data.designation || '');

                    $modal.removeClass('hidden').addClass('flex');
                    $('body').addClass('overflow-hidden');
                }

                $(document).on('click', '.js-open-contact-modal', function () {
                    const $btn = $(this);
                    const mode = $btn.data('mode');
                    openModal(mode, $btn.data('action'), {
                        name: $btn.data('name'),
                        email: $btn.data('email'),
                        phone: $btn.data('phone'),
                        designation: $btn.data('designation'),
                    });
                });

                $(document).on('click', '.js-close-contact-modal', function () {
                    $modal.addClass('hidden').removeClass('flex');
                    $('body').removeClass('overflow-hidden');
                });

                $modalForm.on('submit', function (e) {
                    e.preventDefault();
                    const $formModal = $(this);
                    const submitBtn = $('#contact-modal-submit');
                    submitBtn.prop('disabled', true).addClass('opacity-70');

                    $.ajax({
                        url: $formModal.attr('action'),
                        method: 'POST',
                        data: $formModal.serialize(),
                        headers: { 'Accept': 'application/json' },
                        success: function (resp) {
                            window.location.href = resp.redirect || "{{ route('customers.show', $customer) }}";
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

                $form.on('submit', function (e) {
                    e.preventDefault();
                    const $formEdit = $(this);
                    const submitBtn = $formEdit.find('button[type=\"submit\"]');
                    submitBtn.prop('disabled', true).addClass('opacity-70');

                    $.ajax({
                        url: $formEdit.attr('action'),
                        method: 'POST',
                        data: $formEdit.serialize(),
                        headers: { 'Accept': 'application/json' },
                        success: function (resp) {
                            window.location.href = resp.redirect || "{{ route('customers.show', $customer) }}";
                        },
                        error: function (xhr) {
                            submitBtn.prop('disabled', false).removeClass('opacity-70');
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                                alert(firstError);
                            } else {
                                alert('Unable to update customer right now. Please try again.');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
