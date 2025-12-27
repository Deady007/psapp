const themeKey = 'gearbox_theme';
const themes = new Set(['glass', 'softui', 'terminalwm', 'quickx', 'custom']);
const customKey = 'gearbox_custom_ui';
const particlesKey = 'gearbox_particles_intensity';

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

const readCustomConfig = () => {
    try {
        const raw = localStorage.getItem(customKey);
        return raw ? { ...defaultCustom, ...JSON.parse(raw) } : { ...defaultCustom };
    } catch (error) {
        return { ...defaultCustom };
    }
};

const applyStoredParticles = () => {
    try {
        const stored = localStorage.getItem(particlesKey);
        if (stored && document.body) {
            document.body.dataset.particlesIntensity = stored;
        }
    } catch (error) {
        // ignore
    }
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
    root.style.setProperty('--custom-earth-opacity', config.earthOpacity ?? defaultCustom.earthOpacity);
    root.style.setProperty('--custom-earth-scale', config.earthScale ?? defaultCustom.earthScale);
    root.style.setProperty('--custom-earth-visible', config.earthVisible ? 1 : 0);
};

const readStoredTheme = () => {
    try {
        const stored = localStorage.getItem(themeKey);
        return themes.has(stored) ? stored : null;
    } catch (error) {
        return null;
    }
};

const applyTheme = (theme) => {
    if (!themes.has(theme)) {
        return;
    }

    document.documentElement.dataset.theme = theme;

    if (document.body) {
        document.body.dataset.theme = theme;
    }

    try {
        localStorage.setItem(themeKey, theme);
    } catch (error) {
        // Ignore storage failures (private mode, disabled storage, etc.)
    }

    document.querySelectorAll('[data-theme-switcher]').forEach((select) => {
        if (select.value !== theme) {
            select.value = theme;
        }
    });
};

const initThemeSwitcher = () => {
    const custom = readCustomConfig();
    applyCustomVars(custom);
    applyStoredParticles();

    const storedTheme = readStoredTheme();
    const fallbackTheme = document.body?.dataset.theme || document.documentElement.dataset.theme || 'glass';
    const theme = storedTheme || (themes.has(fallbackTheme) ? fallbackTheme : 'glass');

    applyTheme(theme);

    document.querySelectorAll('[data-theme-switcher]').forEach((select) => {
        select.value = theme;
        select.addEventListener('change', (event) => {
            applyTheme(event.target.value);
        });
    });
};

const storedTheme = readStoredTheme();
if (storedTheme) {
    document.documentElement.dataset.theme = storedTheme;
}

document.addEventListener('DOMContentLoaded', initThemeSwitcher);
