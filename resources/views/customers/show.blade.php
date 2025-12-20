<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-200/80">{{ __('Customer Profile') }}</p>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-3xl font-semibold leading-tight text-white font-display">{{ $customer->name }}</h2>
                    <div class="flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-amber-100 ring-1 ring-white/10">
                        <span class="h-2 w-2 rounded-full {{ $customer->status === 'active' ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                        {{ __($customer->status === 'active' ? 'Active customer' : 'Inactive customer') }}
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 text-xs text-slate-300">
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10 text-amber-100">{{ $customer->projects_count }} {{ __('Projects') }}</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/10 text-amber-100">{{ $customer->contacts_count }} {{ __('Contacts') }}</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    id="customer-edit-toggle"
                    data-mode="view"
                    class="soft-cta"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path d="M5.433 13.917 4.25 15.1v-2.29l7.945-7.945 2.29 2.289-7.052 7.052ZM13.605 1.75l2.645 2.645a.75.75 0 0 1 0 1.06l-1.48 1.48-3.705-3.705 1.48-1.48a.75.75 0 0 1 1.06 0Z" />
                    </svg>
                    {{ __('Edit') }}
                </button>

                <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('{{ __('Delete this customer?') }}')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button class="min-w-0 px-6">{{ __('Delete') }}</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="soft-card p-6 motion-safe:animate-reveal">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Confidence') }}</p>
                    <p class="mt-2 text-2xl font-semibold text-white">{{ __('Priority Customer') }}</p>
                    <p class="mt-3 text-sm text-slate-300">{{ __('Keep this relationship warm with timely follow-ups and a clear project overview.') }}</p>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 motion-safe:animate-reveal reveal-delay-1">
                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Projects') }}</p>
                        <p class="mt-2 text-2xl font-bold text-white">{{ $customer->projects_count }}</p>
                        <a href="{{ route('projects.index', ['customer_id' => $customer->id]) }}" class="text-xs font-semibold text-amber-100 hover:text-amber-50">{{ __('View all') }}</a>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Contacts') }}</p>
                        <p class="mt-2 text-2xl font-bold text-white">{{ $customer->contacts_count }}</p>
                        <a href="#contacts-section" class="text-xs font-semibold text-amber-100 hover:text-amber-50">{{ __('Manage') }}</a>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Status') }}</p>
                        <div class="mt-2 text-sm font-semibold text-white">
                            @if ($customer->status === 'active')
                                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-200 ring-1 ring-emerald-400/30">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span> {{ __('Active') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-200 ring-1 ring-amber-400/30">
                                    <span class="h-2 w-2 rounded-full bg-amber-400"></span> {{ __('Inactive') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="soft-panel overflow-hidden" x-data="{ tab: 'details' }" x-cloak>
                <div class="border-b border-white/10 bg-white/5 px-6 pt-6">
                    <div class="flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="rounded-full px-4 py-2 text-sm font-semibold transition ring-1"
                            :class="tab === 'details' ? 'bg-white/10 text-amber-100 ring-amber-300/40' : 'text-slate-300 ring-white/10 hover:bg-white/10 hover:text-amber-100'"
                            @click="tab = 'details'"
                        >
                            {{ __('Details') }}
                        </button>
                        <button
                            type="button"
                            class="rounded-full px-4 py-2 text-sm font-semibold transition ring-1"
                            :class="tab === 'contacts' ? 'bg-white/10 text-amber-100 ring-amber-300/40' : 'text-slate-300 ring-white/10 hover:bg-white/10 hover:text-amber-100'"
                            @click="tab = 'contacts'"
                        >
                            {{ __('Contacts') }} ({{ $customer->contacts_count }})
                        </button>
                        <a
                            href="{{ route('projects.index', ['customer_id' => $customer->id]) }}"
                            class="rounded-full px-4 py-2 text-sm font-semibold text-slate-300 ring-1 ring-white/10 transition hover:bg-white/10 hover:text-amber-100"
                        >
                            {{ __('Projects') }} ({{ $customer->projects_count }})
                        </a>
                    </div>
                </div>

                <div class="p-6 text-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Overview') }}</p>
                            <h3 class="text-lg font-semibold text-white font-display">{{ __('Customer Details') }}</h3>
                        </div>
                        <button
                            type="button"
                            class="hidden text-sm font-semibold text-amber-100/80 hover:text-amber-50"
                            id="customer-cancel-edit"
                        >
                            {{ __('Cancel') }}
                        </button>
                    </div>

                    <div id="details-section" class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3" x-show="tab === 'details'" x-transition>
                        <div class="lg:col-span-2 flex flex-col gap-6">
                            <div class="grid grid-cols-1 gap-4 rounded-2xl bg-white/5 p-4 ring-1 ring-white/10 sm:grid-cols-2" id="customer-view-section">
                                <div class="rounded-xl bg-slate-900/70 p-4 ring-1 ring-white/10">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Email') }}</p>
                                    <p class="mt-2 text-sm font-semibold text-white">{{ $customer->email ?: __('Not provided') }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-900/70 p-4 ring-1 ring-white/10">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Phone') }}</p>
                                    <p class="mt-2 text-sm font-semibold text-white">{{ $customer->phone ?: __('Not provided') }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-900/70 p-4 ring-1 ring-white/10">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Address') }}</p>
                                    <p class="mt-2 text-sm font-semibold text-white whitespace-pre-line">{{ $customer->address ?: __('Not provided') }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-900/70 p-4 ring-1 ring-white/10">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Pincode') }}</p>
                                    <p class="mt-2 text-sm font-semibold text-white">{{ $customer->pincode ?: __('Not provided') }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-900/70 p-4 ring-1 ring-white/10">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Status') }}</p>
                                    <div class="mt-2">
                                        @if ($customer->status === 'active')
                                            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-200 ring-1 ring-emerald-400/30">
                                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span> {{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-200 ring-1 ring-amber-400/30">
                                                <span class="h-2 w-2 rounded-full bg-amber-400"></span> {{ __('Inactive') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="rounded-xl bg-slate-900/70 p-4 ring-1 ring-white/10 sm:col-span-2">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Notes') }}</p>
                                    <p class="mt-2 text-sm font-semibold text-white whitespace-pre-line">
                                        {{ $customer->notes ?: __('Not provided') }}
                                    </p>
                                </div>
                            </div>

                            <div id="customer-edit-section" class="hidden rounded-2xl bg-slate-900/70 p-6 ring-1 ring-white/10">
                                <form id="customer-inline-form" class="grid grid-cols-1 gap-6 sm:grid-cols-2 js-ajax-customer-update" method="POST" action="{{ route('customers.update', $customer) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="sm:col-span-2">
                                        <x-input-label for="edit_name" :value="__('Name')" class="text-slate-200" />
                                        <x-text-input id="edit_name" name="name" type="text" class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 focus:border-amber-400 focus:ring-amber-400" value="{{ $customer->name }}" required />
                                    </div>
                                    <div>
                                        <x-input-label for="edit_email" :value="__('Email')" class="text-slate-200" />
                                        <x-text-input id="edit_email" name="email" type="email" class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 focus:border-amber-400 focus:ring-amber-400" value="{{ $customer->email }}" />
                                    </div>
                                    <div>
                                        <x-input-label for="edit_phone" :value="__('Phone')" class="text-slate-200" />
                                        <x-text-input id="edit_phone" name="phone" type="text" class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 focus:border-amber-400 focus:ring-amber-400" value="{{ $customer->phone }}" />
                                    </div>
                                    <div>
                                        <x-input-label for="edit_address" :value="__('Address')" class="text-slate-200" />
                                        <textarea id="edit_address" name="address" rows="3" class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 shadow-sm focus:border-amber-400 focus:ring-amber-400">{{ $customer->address }}</textarea>
                                    </div>
                                    <div>
                                        <x-input-label for="edit_pincode" :value="__('Pincode')" class="text-slate-200" />
                                        <x-text-input id="edit_pincode" name="pincode" type="text" class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 focus:border-amber-400 focus:ring-amber-400" value="{{ $customer->pincode }}" />
                                    </div>
                                    <div>
                                        <x-input-label for="edit_status" :value="__('Status')" class="text-slate-200" />
                                        <select
                                            id="edit_status"
                                            name="status"
                                            data-enhance="choices"
                                            class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 shadow-sm focus:border-amber-400 focus:ring-amber-400"
                                            required
                                        >
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status }}" @selected($customer->status === $status)>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <x-input-label for="edit_notes" :value="__('Notes')" class="text-slate-200" />
                                        <textarea id="edit_notes" name="notes" rows="3" class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 shadow-sm focus:border-amber-400 focus:ring-amber-400">{{ $customer->notes }}</textarea>
                                    </div>
                                    <div class="flex items-center gap-4 sm:col-span-2">
                                        <x-primary-button class="min-w-0 px-6">{{ __('Save') }}</x-primary-button>
                                        <button type="button" class="text-sm font-semibold text-amber-100/80 hover:text-amber-50" id="customer-cancel-edit-inline">{{ __('Cancel') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="soft-card p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Quick Notes') }}</p>
                            <p class="mt-2 text-sm text-slate-300">{{ __('Capture recent updates or talking points before your next call.') }}</p>
                            <div class="mt-6 flex flex-col gap-3">
                                <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Primary Contact') }}</p>
                                    <p class="mt-2 text-sm font-semibold text-white">
                                        {{ optional($customer->contacts->first())->name ?? __('Not provided') }}
                                    </p>
                                    <p class="text-xs text-slate-400">{{ optional($customer->contacts->first())->email ?? __('Add a contact to see details') }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Next Step') }}</p>
                                    <p class="mt-2 text-sm text-slate-300">{{ __('Plan your next outreach and update project progress here.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-white/10 bg-white/5 p-6 text-slate-100" id="contacts-section" x-show="tab === 'contacts'" x-transition>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Collaboration') }}</p>
                            <h3 class="text-lg font-semibold text-white font-display">{{ __('Contacts') }}</h3>
                        </div>
                        <button
                            type="button"
                            class="soft-cta text-xs js-open-contact-modal"
                            data-mode="create"
                            data-action="{{ route('customers.contacts.store', $customer) }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M10 3.5a.75.75 0 0 1 .75.75v5h5a.75.75 0 0 1 0 1.5h-5v5a.75.75 0 0 1-1.5 0v-5h-5a.75.75 0 0 1 0-1.5h5v-5A.75.75 0 0 1 10 3.5Z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Add Contact') }}
                        </button>
                    </div>

                    <div class="mt-4 overflow-hidden rounded-2xl bg-slate-900/70 ring-1 ring-white/10">
                        <table class="min-w-full divide-y divide-white/10 text-sm text-slate-200">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">{{ __('Name') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">{{ __('Email') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">{{ __('Phone') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">{{ __('Designation') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @forelse ($customer->contacts as $contact)
                                    <tr class="transition hover:bg-white/5" data-row-id="contact-{{ $contact->id }}">
                                        <td class="px-4 py-3 text-sm font-medium text-white">{{ $contact->name }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-300">{{ $contact->email ?: __('Not provided') }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-300">{{ $contact->phone ?: __('Not provided') }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-300">{{ $contact->designation ?: __('Not provided') }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold text-slate-200">
                                            <div class="flex justify-end gap-3">
                                                <button
                                                    type="button"
                                                    class="text-amber-100 transition hover:text-amber-50 js-open-contact-modal"
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
                                                    <button type="submit" class="text-rose-300 transition hover:text-rose-100">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-300">{{ __('No contacts yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <a href="{{ route('customers.index') }}" class="text-sm font-semibold text-amber-100/80 hover:text-amber-50">
                    {{ __('Back to Customers') }}
                </a>
            </div>
        </div>
    </div>

    <div id="contact-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur">
        <div class="w-full max-w-2xl">
            <div class="w-full overflow-hidden rounded-2xl bg-slate-900/90 shadow-2xl ring-1 ring-white/10">
                <div class="flex items-center justify-between bg-gradient-to-r from-ember-dark via-ember to-gold px-6 py-4 text-white">
                    <h3 class="text-lg font-semibold" id="contact-modal-title">{{ __('Add Contact') }}</h3>
                    <button type="button" class="text-white/80 transition hover:text-white js-close-contact-modal">&times;</button>
                </div>
                <div class="p-6">
                    <form id="contact-modal-form" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @csrf
                        <input type="hidden" name="_method" value="POST">

                        <div>
                            <x-input-label for="modal_contact_name" :value="__('Name')" class="text-slate-200" />
                            <x-text-input id="modal_contact_name" name="name" type="text" class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 focus:border-amber-400 focus:ring-amber-400" required />
                        </div>
                        <div>
                            <x-input-label for="modal_contact_email" :value="__('Email')" class="text-slate-200" />
                            <x-text-input id="modal_contact_email" name="email" type="email" class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 focus:border-amber-400 focus:ring-amber-400" />
                        </div>
                        <div>
                            <x-input-label for="modal_contact_phone" :value="__('Phone')" class="text-slate-200" />
                            <x-text-input id="modal_contact_phone" name="phone" type="text" class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 focus:border-amber-400 focus:ring-amber-400" />
                        </div>
                        <div>
                            <x-input-label for="modal_contact_designation" :value="__('Designation')" class="text-slate-200" />
                            <x-text-input id="modal_contact_designation" name="designation" type="text" class="mt-2 block w-full rounded-xl border-white/10 bg-white/5 text-slate-100 focus:border-amber-400 focus:ring-amber-400" />
                        </div>

                        <div class="sm:col-span-2 flex items-center justify-end gap-3 pt-2">
                            <button type="button" class="text-sm font-semibold text-amber-100/80 hover:text-amber-50 js-close-contact-modal">{{ __('Cancel') }}</button>
                            <x-primary-button id="contact-modal-submit" class="min-w-0 px-5 py-2 text-sm">{{ __('Save') }}</x-primary-button>
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
                });

                const $modal = $('#contact-modal');
                const $modalForm = $('#contact-modal-form');
                const $methodInput = $modalForm.find('input[name="_method"]');
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
                    const submitBtn = $formEdit.find('button[type="submit"]');
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
