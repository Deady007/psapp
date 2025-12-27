<x-app-layout bodyClass="settings-page">
    <x-slot name="header">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between gap-3">
            <div class="d-flex flex-column gap-1">
                <p class="text-xs text-uppercase mb-0 text-emerald-300/70">{{ __('Application') }}</p>
                <h1 class="h4 mb-0 text-emerald-100">{{ __('Workspace Controls') }}</h1>
                <p class="text-sm text-muted mb-0">
                    {{ __('Tune theme, effects, and admin settings without leaving your workflow.') }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" class="kanban-button kanban-button-ghost">
                    {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="d-grid gap-4 settings-stack">
        <div id="ui-theme" class="soft-card p-4 settings-section">
            <div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-lg-between">
                <div>
                    <h4 class="mb-1">{{ __('UI Theme') }}</h4>
                    <p class="text-muted mb-0">{{ __('Switch the visual theme for this workspace.') }}</p>
                </div>
                <select aria-label="{{ __('Theme') }}" data-theme-switcher class="kanban-select w-100 w-lg-auto">
                    <option value="glass">{{ __('Glass') }}</option>
                    <option value="softui">{{ __('SoftUI') }}</option>
                    <option value="terminalwm">{{ __('Terminal WM') }}</option>
                    <option value="quickx">{{ __('QuickX') }}</option>
                    <option value="custom">{{ __('Custom') }}</option>
                </select>
            </div>
        </div>

        <div id="particles" class="soft-card p-4 settings-section">
            <div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-lg-between">
                <div>
                    <h4 class="mb-1">{{ __('Particles') }}</h4>
                    <p class="text-muted mb-0">{{ __('Set particles intensity for the glass theme.') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="kanban-button" data-particles-set="off">{{ __('Off') }}</button>
                    <button type="button" class="kanban-button" data-particles-set="low">{{ __('Low') }}</button>
                    <button type="button" class="kanban-button" data-particles-set="med">{{ __('Medium') }}</button>
                    <button type="button" class="kanban-button" data-particles-set="high">{{ __('High') }}</button>
                </div>
            </div>
        </div>

        <div id="custom-ui" class="soft-card p-4 settings-section">
            <div class="d-flex flex-column gap-3">
                <div class="d-flex flex-column flex-lg-row gap-2 align-items-lg-center justify-content-lg-between">
                    <div>
                        <h4 class="mb-1">{{ __('Custom UI') }}</h4>
                        <p class="text-muted mb-0">{{ __('Choose your own colors and effects for the custom theme.') }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="kanban-button" id="custom-theme-apply">{{ __('Apply Custom Theme') }}</button>
                        <button type="button" class="kanban-button kanban-button-ghost" id="custom-theme-reset">{{ __('Reset') }}</button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Background') }}</label>
                        <input type="color" class="form-control" id="custom-bg" value="#0b0f15">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Surface') }}</label>
                        <input type="color" class="form-control" id="custom-surface" value="#0f1115">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Surface 2') }}</label>
                        <input type="color" class="form-control" id="custom-surface-2" value="#1a1f28">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Text') }}</label>
                        <input type="color" class="form-control" id="custom-text" value="#e2e8f0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Accent') }}</label>
                        <input type="color" class="form-control" id="custom-accent" value="#22d3ee">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Border') }}</label>
                        <input type="color" class="form-control" id="custom-border" value="#3f3f46">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Radius (px)') }}</label>
                        <input type="range" class="form-control-range w-100" id="custom-radius" min="6" max="28" step="1" value="18">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Blur (px)') }}</label>
                        <input type="range" class="form-control-range w-100" id="custom-blur" min="0" max="30" step="1" value="12">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Glow (px)') }}</label>
                        <input type="range" class="form-control-range w-100" id="custom-glow" min="0" max="36" step="1" value="20">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Background Accent 1') }}</label>
                        <input type="color" class="form-control" id="custom-radial-1" value="#00ff75">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Background Accent 2') }}</label>
                        <input type="color" class="form-control" id="custom-radial-2" value="#7cff9c">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Background Accent 3') }}</label>
                        <input type="color" class="form-control" id="custom-radial-3" value="#2dd4bf">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Grid Opacity') }}</label>
                        <input type="range" class="form-control-range w-100" id="custom-grid-opacity" min="0" max="1" step="0.05" value="0.25">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Particles Opacity') }}</label>
                        <input type="range" class="form-control-range w-100" id="custom-particles-opacity" min="0" max="1" step="0.05" value="0.55">
                    </div>
                    <div class="col-md-4 form-check d-flex align-items-center gap-2 mt-4">
                        <input type="checkbox" class="form-check-input" id="custom-earth-visible" checked>
                        <label class="form-check-label mb-0" for="custom-earth-visible">{{ __('Show Globe') }}</label>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Globe Color') }}</label>
                        <input type="color" class="form-control" id="custom-earth-color" value="#22d3ee">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Globe Opacity') }}</label>
                        <input type="range" class="form-control-range w-100" id="custom-earth-opacity" min="0" max="1" step="0.05" value="0.6">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Globe Scale') }}</label>
                        <input type="range" class="form-control-range w-100" id="custom-earth-scale" min="0.5" max="1.5" step="0.05" value="1">
                    </div>
                </div>
            </div>
        </div>

        <div id="admin" class="soft-card p-4 settings-section">
            <details open class="d-flex flex-column gap-3">
                <summary class="d-flex flex-column flex-lg-row gap-2 align-items-lg-center justify-content-lg-between">
                    <div>
                        <h4 class="mb-1 mb-lg-0">{{ __('Admin') }}</h4>
                        <p class="text-muted mb-0">{{ __('Manage users, roles, and permissions.') }}</p>
                    </div>
                </summary>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.index') }}" class="kanban-button kanban-button-ghost">{{ __('Users') }}</a>
                    <a href="{{ route('admin.roles.index') }}" class="kanban-button kanban-button-ghost">{{ __('Roles') }}</a>
                    <a href="{{ route('admin.permissions.index') }}" class="kanban-button kanban-button-ghost">{{ __('Permissions') }}</a>
                </div>
            </details>
        </div>

        <div id="profile" class="soft-card p-4 settings-section">
            <div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-lg-between">
                <div>
                    <h4 class="mb-1">{{ __('Profile & Preferences') }}</h4>
                    <p class="text-muted mb-0">{{ __('Update your profile and security settings.') }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="kanban-button kanban-button-ghost">
                    {{ __('Profile management') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sections = Array.from(document.querySelectorAll('.settings-section'));

            const setParticles = (value) => {
                localStorage.setItem('gearbox_particles_intensity', value);
                document.body.dataset.particlesIntensity = value;
            };

            document.querySelectorAll('[data-particles-set]').forEach((btn) => {
                btn.addEventListener('click', () => setParticles(btn.dataset.particlesSet));
            });

            const applyHashFilter = () => {
                const hash = window.location.hash.replace('#', '');
                if (!hash) {
                    sections.forEach((section) => (section.hidden = false));
                    return;
                }

                let matched = false;
                sections.forEach((section) => {
                    if (section.id === hash) {
                        section.hidden = false;
                        matched = true;
                    } else {
                        section.hidden = true;
                    }
                });

                if (!matched) {
                    sections.forEach((section) => (section.hidden = false));
                } else {
                    const target = document.getElementById(hash);
                    target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            };

            applyHashFilter();
            window.addEventListener('hashchange', applyHashFilter);

            const defaultCustom = {
                bg: '#0b0f15',
                surface: '#0f1115',
                surface2: '#1a1f28',
                text: '#e2e8f0',
                accent: '#22d3ee',
                border: '#3f3f46',
                radius: '18',
                blur: '12',
                glow: '20',
                radial1: '#00ff75',
                radial2: '#7cff9c',
                radial3: '#2dd4bf',
                gridOpacity: '0.25',
                particlesOpacity: '0.55',
                earthVisible: true,
                earthColor: '#22d3ee',
                earthOpacity: '0.6',
                earthScale: '1',
            };

            const customInputs = {
                bg: document.getElementById('custom-bg'),
                surface: document.getElementById('custom-surface'),
                surface2: document.getElementById('custom-surface-2'),
                text: document.getElementById('custom-text'),
                accent: document.getElementById('custom-accent'),
                border: document.getElementById('custom-border'),
                radius: document.getElementById('custom-radius'),
                blur: document.getElementById('custom-blur'),
                glow: document.getElementById('custom-glow'),
                radial1: document.getElementById('custom-radial-1'),
                radial2: document.getElementById('custom-radial-2'),
                radial3: document.getElementById('custom-radial-3'),
                gridOpacity: document.getElementById('custom-grid-opacity'),
                particlesOpacity: document.getElementById('custom-particles-opacity'),
                earthVisible: document.getElementById('custom-earth-visible'),
                earthColor: document.getElementById('custom-earth-color'),
                earthOpacity: document.getElementById('custom-earth-opacity'),
                earthScale: document.getElementById('custom-earth-scale'),
            };

            const loadCustomConfig = () => {
                try {
                    const raw = localStorage.getItem('gearbox_custom_ui');
                    return raw ? { ...defaultCustom, ...JSON.parse(raw) } : { ...defaultCustom };
                } catch (error) {
                    return { ...defaultCustom };
                }
            };

            const saveCustomConfig = (config) => {
                localStorage.setItem('gearbox_custom_ui', JSON.stringify(config));
            };

            const applyCustomVars = (config) => {
                const root = document.documentElement;
                const glowPx = Number(config.glow) || Number(defaultCustom.glow);
                const radiusPx = Number(config.radius) || Number(defaultCustom.radius);
                const blurPx = Number(config.blur) || Number(defaultCustom.blur);
                const accent = config.accent || defaultCustom.accent;

                root.style.setProperty('--custom-bg', config.bg || defaultCustom.bg);
                root.style.setProperty('--custom-surface', config.surface || defaultCustom.surface);
                root.style.setProperty('--custom-surface-2', config.surface2 || defaultCustom.surface2);
                root.style.setProperty('--custom-text', config.text || defaultCustom.text);
                root.style.setProperty('--custom-muted', `color-mix(in srgb, ${config.text || defaultCustom.text} 70%, transparent)`);
                root.style.setProperty('--custom-border', config.border || defaultCustom.border);
                root.style.setProperty('--custom-accent', accent);
                root.style.setProperty('--custom-accent-2', accent);
                root.style.setProperty('--custom-focus', accent);
                root.style.setProperty('--custom-radius', `${radiusPx}px`);
                root.style.setProperty('--custom-blur', `${blurPx}px`);
                root.style.setProperty('--custom-glow', `0 0 ${glowPx}px color-mix(in srgb, ${accent} 55%, transparent)`);
                root.style.setProperty('--custom-radial-1', config.radial1 || defaultCustom.radial1);
                root.style.setProperty('--custom-radial-2', config.radial2 || defaultCustom.radial2);
                root.style.setProperty('--custom-radial-3', config.radial3 || defaultCustom.radial3);
                root.style.setProperty('--custom-before-1', config.radial1 || defaultCustom.radial1);
                root.style.setProperty('--custom-before-2', config.radial2 || defaultCustom.radial2);
                root.style.setProperty('--custom-before-3', config.radial3 || defaultCustom.radial3);
                root.style.setProperty('--custom-grid-opacity', config.gridOpacity ?? defaultCustom.gridOpacity);
                root.style.setProperty('--custom-particles', config.particlesOpacity ?? defaultCustom.particlesOpacity);
                root.style.setProperty('--custom-earth', config.earthColor || defaultCustom.earthColor);
                root.style.setProperty('--custom-earth-opacity-val', config.earthOpacity ?? defaultCustom.earthOpacity);
                root.style.setProperty('--custom-earth-scale-val', config.earthScale ?? defaultCustom.earthScale);
                root.style.setProperty('--custom-earth-visible-val', config.earthVisible ? 1 : 0);
            };

            const syncInputs = (config) => {
                Object.entries(customInputs).forEach(([key, input]) => {
                    if (!input) {
                        return;
                    }
                    if (input.type === 'checkbox') {
                        input.checked = Boolean(config[key]);
                        return;
                    }
                    if (config[key] !== undefined && config[key] !== null) {
                        input.value = config[key];
                    }
                });
            };

            const readConfigFromInputs = () => ({
                bg: customInputs.bg?.value || defaultCustom.bg,
                surface: customInputs.surface?.value || defaultCustom.surface,
                surface2: customInputs.surface2?.value || defaultCustom.surface2,
                text: customInputs.text?.value || defaultCustom.text,
                accent: customInputs.accent?.value || defaultCustom.accent,
                border: customInputs.border?.value || defaultCustom.border,
                radius: customInputs.radius?.value || defaultCustom.radius,
                blur: customInputs.blur?.value || defaultCustom.blur,
                glow: customInputs.glow?.value || defaultCustom.glow,
                radial1: customInputs.radial1?.value || defaultCustom.radial1,
                radial2: customInputs.radial2?.value || defaultCustom.radial2,
                radial3: customInputs.radial3?.value || defaultCustom.radial3,
                gridOpacity: customInputs.gridOpacity?.value || defaultCustom.gridOpacity,
                particlesOpacity: customInputs.particlesOpacity?.value || defaultCustom.particlesOpacity,
                earthVisible: Boolean(customInputs.earthVisible?.checked),
                earthColor: customInputs.earthColor?.value || defaultCustom.earthColor,
                earthOpacity: customInputs.earthOpacity?.value || defaultCustom.earthOpacity,
                earthScale: customInputs.earthScale?.value || defaultCustom.earthScale,
            });

            const setThemeToCustom = () => {
                if (typeof applyTheme === 'function') {
                    applyTheme('custom');
                } else {
                    document.documentElement.dataset.theme = 'custom';
                    document.body.dataset.theme = 'custom';
                }
                localStorage.setItem('gearbox_theme', 'custom');
            };

            const restore = loadCustomConfig();
            syncInputs(restore);
            applyCustomVars(restore);
            if (localStorage.getItem('gearbox_theme') === 'custom') {
                setThemeToCustom();
            }

            document.getElementById('custom-theme-apply')?.addEventListener('click', () => {
                const config = readConfigFromInputs();
                saveCustomConfig(config);
                applyCustomVars(config);
                setThemeToCustom();
            });

            document.getElementById('custom-theme-reset')?.addEventListener('click', () => {
                syncInputs(defaultCustom);
                saveCustomConfig(defaultCustom);
                applyCustomVars(defaultCustom);
                setThemeToCustom();
            });

        });
    </script>
</x-app-layout>
