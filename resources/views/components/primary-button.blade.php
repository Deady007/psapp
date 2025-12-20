<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex h-11 min-w-[140px] items-center justify-center gap-2 rounded-full bg-gradient-to-r from-ember-dark via-ember to-gold px-5 text-sm font-semibold uppercase tracking-wide text-white shadow-lg shadow-amber-500/40 transition-all duration-300 ease-out hover:-translate-y-0.5 hover:shadow-amber-300/70 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-300/70 active:translate-y-0',
]) }}>
    {{ $slot }}
</button>
