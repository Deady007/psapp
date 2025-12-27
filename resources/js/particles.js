const intensityMap = {
    off: 0,
    low: 24,
    med: 48,
    high: 96,
};

const readAccent = () => {
    const root = getComputedStyle(document.documentElement);

    return root.getPropertyValue('--accent').trim() || '#7cf5ff';
};

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

const buildCanvas = () => {
    const canvas = document.createElement('canvas');
    canvas.className = 'particles-canvas';
    canvas.setAttribute('aria-hidden', 'true');

    return canvas;
};

const buildControl = (current, onChange) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'particles-control';
    wrapper.innerHTML = `
        <span>Particles</span>
        <select aria-label="Particles intensity">
            <option value="off">Off</option>
            <option value="low">Low</option>
            <option value="med">Med</option>
            <option value="high">High</option>
        </select>
    `;

    const select = wrapper.querySelector('select');

    if (select) {
        select.value = current;
        select.addEventListener('change', () => {
            onChange(select.value);
        });
    }

    return wrapper;
};

const initParticles = () => {
    const body = document.body;
    if (!body || body.dataset.theme !== 'glass') {
        return;
    }

    const stored = localStorage.getItem('gearbox_particles_intensity');
    const defaultIntensity = body.dataset.particlesIntensity || 'med';
    let intensity = stored || defaultIntensity;
    const prefersReduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const allowMotion = body.dataset.motion !== '0';
    if (!allowMotion || prefersReduce) {
        intensity = 'off';
    }

    const canvas = buildCanvas();
    const ctx = canvas.getContext('2d');

    if (!ctx) {
        return;
    }

    body.prepend(canvas);

    let particles = [];
    let animationFrame = null;

    const resize = () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    };

    const color = readAccent();

    const spawnParticles = (count) => {
        particles = Array.from({ length: count }).map(() => ({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            r: Math.random() * 2.2 + 0.6,
            vx: (Math.random() - 0.5) * 0.4,
            vy: (Math.random() - 0.5) * 0.4,
        }));
    };

    const tick = () => {
        if (!ctx) {
            return;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = color;
        ctx.strokeStyle = color;
        ctx.globalAlpha = 0.8;

        particles.forEach((p, idx) => {
            p.x += p.vx;
            p.y += p.vy;

            if (p.x < 0 || p.x > canvas.width) {
                p.vx *= -1;
            }

            if (p.y < 0 || p.y > canvas.height) {
                p.vy *= -1;
            }

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fill();

            for (let i = idx + 1; i < particles.length; i += 1) {
                const other = particles[i];
                const dx = p.x - other.x;
                const dy = p.y - other.y;
                const dist = Math.hypot(dx, dy);

                if (dist < 120) {
                    ctx.globalAlpha = clamp(1 - dist / 120, 0.05, 0.4);
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(other.x, other.y);
                    ctx.stroke();
                    ctx.globalAlpha = 0.8;
                }
            }
        });

        animationFrame = requestAnimationFrame(tick);
    };

    const updateIntensity = (value) => {
        intensity = value;
        body.dataset.particlesIntensity = value;
        localStorage.setItem('gearbox_particles_intensity', value);

        const count = intensityMap[value] ?? intensityMap.med;
        spawnParticles(count);
    };

    window.addEventListener('resize', resize);
    resize();
    updateIntensity(intensity);
    tick();

    const control = buildControl(intensity, updateIntensity);
    body.append(control);

    const observer = new MutationObserver(() => {
        if (body.dataset.theme !== 'glass') {
            particles = [];
        }
    });

    observer.observe(body, { attributes: true, attributeFilter: ['data-theme'] });
};

document.addEventListener('DOMContentLoaded', initParticles);
