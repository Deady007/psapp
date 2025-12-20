<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-200/80">{{ __('Admin') }}</p>
                <h2 class="text-3xl font-semibold leading-tight text-white font-display">
                    {{ __('Roles') }}
                </h2>
                <p class="text-sm text-slate-300">{{ __('Shape responsibilities with curated role profiles.') }}</p>
            </div>

            <a href="{{ route('admin.roles.create') }}" class="soft-cta">
                {{ __('New Role') }}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h5.5V3.75a.75.75 0 0 1 1.5 0v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="soft-panel overflow-hidden motion-safe:animate-reveal">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-6 py-4">
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Directory') }}</p>
                        <h3 class="text-lg font-semibold text-white font-display">{{ __('Role list') }}</h3>
                    </div>
                    <p class="text-xs text-slate-400">
                        {{ __('Showing') }} {{ $roles->firstItem() ?? 0 }}-{{ $roles->lastItem() ?? 0 }} {{ __('of') }} {{ $roles->total() }}
                    </p>
                </div>

                @if ($roles->isEmpty())
                    <div class="px-6 py-8 text-sm text-slate-300">{{ __('No roles found.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">{{ __('Name') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">{{ __('Permissions') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach ($roles as $role)
                                    <tr class="group transition hover:bg-white/5">
                                        <td class="px-4 py-3 text-sm">
                                            <a href="{{ route('admin.roles.show', $role) }}" class="font-semibold text-white transition group-hover:text-amber-200">
                                                {{ $role->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            @if ($role->permissions->isEmpty())
                                                <span class="text-slate-400">ƒ?"</span>
                                            @else
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($role->permissions as $perm)
                                                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-amber-100 ring-1 ring-white/10">{{ $perm->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('admin.roles.edit', $role) }}" class="text-slate-200 transition hover:text-white">
                                                    {{ __('Edit') }}
                                                </a>
                                                @if (! in_array($role->name, ['admin', 'user'], true))
                                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline" onsubmit="return confirm('{{ __('Delete this role?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-rose-300 transition hover:text-rose-100">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-white/10 px-6 py-4">
                        {{ $roles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
