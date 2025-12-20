@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent bg-gradient-to-r from-indigo-600 via-sky-500 to-emerald-400 text-start text-base font-semibold text-white shadow-md shadow-indigo-200/70 focus:outline-none focus:ring-2 focus:ring-indigo-200 transition duration-200 ease-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-700 hover:text-slate-900 hover:bg-gradient-to-r hover:from-indigo-50 hover:via-sky-50 hover:to-emerald-50 hover:border-indigo-100 focus:outline-none focus:text-slate-900 focus:bg-gradient-to-r focus:from-indigo-50 focus:via-sky-50 focus:to-emerald-50 focus:border-indigo-100 transition duration-200 ease-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
