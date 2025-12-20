<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-200/80">{{ __('Admin') }}</p>
                <h2 class="text-3xl font-semibold leading-tight text-white font-display">
                    {{ __('Permissions') }}
                </h2>
                <p class="text-sm text-slate-300">{{ __('Review and tune granular access rules.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-4xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="soft-panel overflow-hidden motion-safe:animate-reveal">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-6 py-4">
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Directory') }}</p>
                        <h3 class="text-lg font-semibold text-white font-display">{{ __('Permission list') }}</h3>
                    </div>
                    <p class="text-xs text-slate-400">
                        {{ __('Showing') }} {{ $permissions->firstItem() ?? 0 }}-{{ $permissions->lastItem() ?? 0 }} {{ __('of') }} {{ $permissions->total() }}
                    </p>
                </div>

                @if ($permissions->isEmpty())
                    <div class="px-6 py-8 text-sm text-slate-300">{{ __('No permissions found.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">{{ __('Name') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach ($permissions as $permission)
                                    <tr class="group transition hover:bg-white/5">
                                        <td class="px-4 py-3 text-sm">
                                            <a href="{{ route('admin.permissions.show', $permission) }}" class="font-semibold text-white transition group-hover:text-amber-200">
                                                {{ $permission->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="text-slate-200 transition hover:text-white">
                                                    {{ __('Edit') }}
                                                </a>
                                                <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" class="inline" onsubmit="return confirm('{{ __('Delete this permission?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-300 transition hover:text-rose-100">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-white/10 px-6 py-4">
                        {{ $permissions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
