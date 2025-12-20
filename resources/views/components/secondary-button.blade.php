<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex h-11 min-w-[140px] items-center justify-center gap-2 rounded-full bg-white/10 px-5 text-sm font-semibold uppercase tracking-wide text-amber-100 shadow-md shadow-black/40 ring-1 ring-amber-400/40 transition-all duration-300 ease-out hover:-translate-y-0.5 hover:bg-white/20 hover:shadow-lg hover:shadow-amber-300/40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-200 disabled:opacity-50 active:translate-y-0',
]) }}>
    {{ $slot }}
</button>
