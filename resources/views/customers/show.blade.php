<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap align-items-center">
                    <h1 class="m-0 mr-3">{{ $customer->name }}</h1>
                    @if ($customer->status === 'active')
                        <span class="badge badge-success">{{ __('Active') }}</span>
                    @else
                        <span class="badge badge-warning">{{ __('Inactive') }}</span>
                    @endif
                </div>
                <div class="text-muted">{{ __('Customer profile') }}</div>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                <button
                    type="button"
                    id="customer-edit-toggle"
                    data-mode="view"
                    class="btn btn-outline-primary mr-2"
                >
                    <i class="fas fa-edit mr-1"></i>
                    {{ __('Edit') }}
                </button>
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline" data-confirm="{{ __('Delete this customer?') }}" data-confirm-button="{{ __('Yes, delete it') }}" data-cancel-button="{{ __('Cancel') }}">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>{{ __('Delete') }}</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-lg-4 col-md-6">
            <x-adminlte-info-box
                title="{{ __('Projects') }}"
                text="{{ $customer->projects_count }}"
                icon="fas fa-briefcase"
                theme="info"
                url="{{ route('projects.index', ['customer_id' => $customer->id]) }}"
            />
        </div>
        <div class="col-lg-4 col-md-6">
            <x-adminlte-info-box
                title="{{ __('Contacts') }}"
                text="{{ $customer->contacts_count }}"
                icon="fas fa-address-book"
                theme="success"
                url="{{ route('customers.contacts.index', $customer) }}"
            />
        </div>
        <div class="col-lg-4 col-md-6">
            <x-adminlte-info-box
                title="{{ __('Status') }}"
                text="{{ __($customer->status) }}"
                icon="fas fa-signal"
                theme="warning"
            />
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#customer-details" role="tab">
                        {{ __('Details') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#customer-contacts" role="tab">
                        {{ __('Contacts') }} ({{ $customer->contacts_count }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('projects.index', ['customer_id' => $customer->id]) }}">
                        {{ __('Projects') }} ({{ $customer->projects_count }})
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="customer-details" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8">
                            <div id="customer-view-section">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <tr>
                                            <th class="text-muted">{{ __('Email') }}</th>
                                            <td>{{ $customer->email ?: __('Not provided') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">{{ __('Phone') }}</th>
                                            <td>{{ $customer->phone ?: __('Not provided') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">{{ __('Address') }}</th>
                                            <td style="white-space: pre-line;">{{ $customer->address ?: __('Not provided') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">{{ __('Pincode') }}</th>
                                            <td>{{ $customer->pincode ?: __('Not provided') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">{{ __('Status') }}</th>
                                            <td>{{ __($customer->status) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">{{ __('Notes') }}</th>
                                            <td style="white-space: pre-line;">{{ $customer->notes ?: __('Not provided') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div id="customer-edit-section" class="d-none">
                                <form id="customer-inline-form" class="js-ajax-customer-update" method="POST" action="{{ route('customers.update', $customer) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <x-input-label for="edit_name" :value="__('Name')" />
                                            <x-text-input id="edit_name" name="name" type="text" :value="$customer->name" required />
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <x-input-label for="edit_email" :value="__('Email')" />
                                            <x-text-input id="edit_email" name="email" type="email" :value="$customer->email" />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <x-input-label for="edit_phone" :value="__('Phone')" />
                                            <x-text-input id="edit_phone" name="phone" type="text" :value="$customer->phone" />
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <x-input-label for="edit_address" :value="__('Address')" />
                                            <textarea id="edit_address" name="address" rows="3" class="form-control">{{ $customer->address }}</textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <x-input-label for="edit_pincode" :value="__('Pincode')" />
                                            <x-text-input id="edit_pincode" name="pincode" type="text" :value="$customer->pincode" />
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <x-input-label for="edit_status" :value="__('Status')" />
                                            <select id="edit_status" name="status" data-control="select2" class="form-control" required>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}" @selected($customer->status === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <x-input-label for="edit_notes" :value="__('Notes')" />
                                            <textarea id="edit_notes" name="notes" rows="3" class="form-control" data-richtext="summernote">{{ $customer->notes }}</textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <x-primary-button class="mr-2">{{ __('Save') }}</x-primary-button>
                                        <button type="button" id="customer-cancel-edit-inline" class="btn btn-outline-secondary">
                                            {{ __('Cancel') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('Quick Notes') }}</h3>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">
                                        {{ __('Capture recent updates or talking points before your next call.') }}
                                    </p>
                                    <div class="mb-3">
                                        <h6 class="mb-1">{{ __('Primary Contact') }}</h6>
                                        <div>{{ optional($customer->contacts->first())->name ?? __('Not provided') }}</div>
                                        <div class="text-muted small">{{ optional($customer->contacts->first())->email ?? __('Add a contact to see details') }}</div>
                                    </div>
                                    <div class="text-muted">
                                        {{ __('Plan your next outreach and update project progress here.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="customer-contacts" role="tabpanel">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">{{ __('Contacts') }}</h5>
                        <button
                            type="button"
                            class="btn btn-sm btn-primary js-open-contact-modal"
                            data-mode="create"
                            data-action="{{ route('customers.contacts.store', $customer) }}"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            {{ __('Add Contact') }}
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th class="text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customer->contacts as $contact)
                                    <tr data-row-id="contact-{{ $contact->id }}">
                                        <td class="font-weight-bold">{{ $contact->name }}</td>
                                        <td>{{ $contact->email ?: __('Not provided') }}</td>
                                        <td>{{ $contact->phone ?: __('Not provided') }}</td>
                                        <td>{{ $contact->designation ?: __('Not provided') }}</td>
                                        <td class="text-right">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary js-open-contact-modal"
                                                data-mode="edit"
                                                data-action="{{ route('customers.contacts.update', [$customer, $contact]) }}"
                                                data-name="{{ $contact->name }}"
                                                data-email="{{ $contact->email }}"
                                                data-phone="{{ $contact->phone }}"
                                                data-designation="{{ $contact->designation }}"
                                            >
                                                {{ __('Edit') }}
                                            </button>

                                            <form method="POST" action="{{ route('customers.contacts.destroy', [$customer, $contact]) }}" class="d-inline js-ajax-delete" data-row="contact-{{ $contact->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">{{ __('No contacts yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('customers.index') }}" class="btn btn-link">
        {{ __('Back to Customers') }}
    </a>

    <div class="modal fade" id="contact-modal" tabindex="-1" role="dialog" aria-labelledby="contact-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="contact-modal-form">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="contact-modal-title">{{ __('Add Contact') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <x-input-label for="modal_contact_name" :value="__('Name')" />
                                <x-text-input id="modal_contact_name" name="name" type="text" required />
                            </div>
                            <div class="form-group col-md-6">
                                <x-input-label for="modal_contact_email" :value="__('Email')" />
                                <x-text-input id="modal_contact_email" name="email" type="email" />
                            </div>
                            <div class="form-group col-md-6">
                                <x-input-label for="modal_contact_phone" :value="__('Phone')" />
                                <x-text-input id="modal_contact_phone" name="phone" type="text" />
                            </div>
                            <div class="form-group col-md-6">
                                <x-input-label for="modal_contact_designation" :value="__('Designation')" />
                                <x-text-input id="modal_contact_designation" name="designation" type="text" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <x-primary-button id="contact-modal-submit">{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('css')
        <style>
            #contact-modal {
                z-index: 2050;
            }

            .contact-modal-backdrop {
                z-index: 2040 !important;
            }
        </style>
    @endpush

    @push('js')
        <script>
            $(function () {
                const $view = $('#customer-view-section');
                const $edit = $('#customer-edit-section');
                const $editBtn = $('#customer-edit-toggle');
                const $cancelInline = $('#customer-cancel-edit-inline');
                const $form = $('#customer-inline-form');

                function setEditButton(mode) {
                    if (mode === 'edit') {
                        $editBtn.data('mode', 'edit').html('<i class="fas fa-save mr-1"></i>{{ __('Save') }}');
                    } else {
                        $editBtn.data('mode', 'view').html('<i class="fas fa-edit mr-1"></i>{{ __('Edit') }}');
                    }
                }

                function enterEdit() {
                    $view.addClass('d-none');
                    $edit.removeClass('d-none');
                    setEditButton('edit');
                    $('#edit_name').trigger('focus');
                }

                function exitEdit() {
                    $view.removeClass('d-none');
                    $edit.addClass('d-none');
                    setEditButton('view');
                }

                $editBtn.on('click', function () {
                    if ($editBtn.data('mode') === 'view') {
                        enterEdit();
                    } else {
                        $form.trigger('submit');
                    }
                });

                $cancelInline.on('click', exitEdit);

                $('.js-ajax-delete').on('submit', function (e) {
                    e.preventDefault();
                    const $formDel = $(this);
                    const targetRow = $formDel.data('row');
                    const confirmText = '{{ __('Delete this contact?') }}';
                    const confirmButtonText = '{{ __('Yes, delete it') }}';
                    const cancelButtonText = '{{ __('Cancel') }}';

                    const performDelete = () => {
                        $.ajax({
                            url: $formDel.attr('action'),
                            method: 'POST',
                            data: $formDel.serialize(),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function () {
                                $('[data-row-id="' + targetRow + '"]').fadeOut(200, function () {
                                    $(this).remove();
                                });
                            },
                            error: function () {
                                alert('Unable to delete contact right now. Please try again.');
                            }
                        });
                    };

                    if (window.Swal) {
                        window.Swal.fire({
                            title: confirmText,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText,
                            cancelButtonText,
                            focusCancel: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                performDelete();
                            }
                        });
                    } else if (confirm(confirmText)) {
                        performDelete();
                    }
                });

                const $modal = $('#contact-modal');
                const $modalForm = $('#contact-modal-form');
                const $methodInput = $modalForm.find('input[name="_method"]');
                const $title = $('#contact-modal-title');
                const $submit = $('#contact-modal-submit');

                $modal.appendTo('body');
                $modal.on('shown.bs.modal', function () {
                    $('#modal_contact_name').trigger('focus');
                    $('.modal-backdrop').addClass('contact-modal-backdrop');
                });

                function openModal(mode, action, data = {}) {
                    $modalForm.attr('action', action);
                    $methodInput.val(mode === 'edit' ? 'PUT' : 'POST');
                    $title.text(mode === 'edit' ? '{{ __('Edit Contact') }}' : '{{ __('Add Contact') }}');

                    $('#modal_contact_name').val(data.name || '');
                    $('#modal_contact_email').val(data.email || '');
                    $('#modal_contact_phone').val(data.phone || '');
                    $('#modal_contact_designation').val(data.designation || '');

                    $modal.modal('show');
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

                $modalForm.on('submit', function (e) {
                    e.preventDefault();
                    const $formModal = $(this);
                    $submit.prop('disabled', true);

                    $.ajax({
                        url: $formModal.attr('action'),
                        method: 'POST',
                        data: $formModal.serialize(),
                        headers: { 'Accept': 'application/json' },
                        success: function (resp) {
                            window.location.href = resp.redirect || "{{ route('customers.show', $customer) }}";
                        },
                        error: function (xhr) {
                            $submit.prop('disabled', false);
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
                    const submitBtn = $formEdit.find('button[type="submit"]');
                    submitBtn.prop('disabled', true);

                    $.ajax({
                        url: $formEdit.attr('action'),
                        method: 'POST',
                        data: $formEdit.serialize(),
                        headers: { 'Accept': 'application/json' },
                        success: function (resp) {
                            window.location.href = resp.redirect || "{{ route('customers.show', $customer) }}";
                        },
                        error: function (xhr) {
                            submitBtn.prop('disabled', false);
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
