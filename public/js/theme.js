document.addEventListener('DOMContentLoaded', () => {
    const toastStack = document.querySelector('.toast-stack');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const removeToast = (toast) => {
        if (!toast) {
            return;
        }
        toast.style.transition = 'opacity 0.2s ease-out, transform 0.2s ease-out';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-8px)';
        window.setTimeout(() => toast.remove(), 200);
    };

    if (toastStack) {
        toastStack.addEventListener('click', (event) => {
            const button = event.target.closest('.toast-close');
            if (button) {
                removeToast(button.closest('.toast-card'));
            }
        });

        toastStack.querySelectorAll('.toast-card[data-autodismiss="true"]').forEach((toast) => {
            window.setTimeout(() => removeToast(toast), 4000);
        });
    }

    window.showToast = (message, type = 'success') => {
        if (!toastStack) {
            return;
        }

        const toast = document.createElement('div');
        toast.className = `toast-card toast-${type}`;
        toast.setAttribute('role', 'status');
        toast.innerHTML = `
            <div class="toast-message"></div>
            <button type="button" class="toast-close" aria-label="Close">&times;</button>
        `;
        toast.querySelector('.toast-message').textContent = message;
        toastStack.appendChild(toast);
        window.setTimeout(() => removeToast(toast), 4000);
    };

    const animateCountup = (element, targetValue) => {
        if (prefersReducedMotion) {
            element.textContent = targetValue;
            return;
        }

        const duration = 500;
        const start = window.performance.now();
        const startValue = 0;

        const step = (timestamp) => {
            const progress = Math.min((timestamp - start) / duration, 1);
            const value = Math.round(startValue + (targetValue - startValue) * progress);
            element.textContent = value.toString();

            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };

        window.requestAnimationFrame(step);
    };

    document.querySelectorAll('[data-countup]').forEach((element) => {
        if (element.dataset.countupDone === '1') {
            return;
        }

        const target = Number(element.dataset.countup);
        if (!Number.isFinite(target)) {
            return;
        }

        animateCountup(element, target);
        element.dataset.countupDone = '1';
    });
});
