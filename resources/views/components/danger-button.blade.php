<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex h-11 min-w-[140px] items-center justify-center gap-2 rounded-full bg-gradient-to-r from-ember-dark via-ember to-ember-bright px-5 text-sm font-semibold uppercase tracking-wide text-white shadow-lg shadow-red-900/50 transition-all duration-300 ease-out hover:-translate-y-0.5 hover:shadow-red-300/70 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-300 active:translate-y-0',
]) }}>
    {{ $slot }}
</button>
