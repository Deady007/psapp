@props(['active'])

@php
$classes = ($active ?? false)
            ? 'group inline-flex w-full items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 text-sm font-semibold leading-5 text-emerald-50 shadow-lg shadow-emerald-500/10 ring-1 ring-emerald-400/40 transition-all duration-200 ease-out hover:-translate-y-0.5'
            : 'group inline-flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium leading-5 text-emerald-100/80 transition-all duration-200 ease-out hover:-translate-y-0.5 hover:bg-white/5 hover:text-emerald-50 hover:ring-1 hover:ring-emerald-300/20';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
