(function () {
    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function isMobile() {
        return window.innerWidth < 768;
    }

    function getConfig() {
        var mobile = isMobile();

        return {
            mobile: mobile,
            pointCount: mobile ? 320 : 780,
            dotSize: mobile ? 1.2 : 1.6,
            radiusScale: mobile ? 0.42 : 0.46,
            depth: mobile ? 1.4 : 1.6,
            rotationSpeed: mobile ? 0.00035 : 0.00025,
            frameInterval: mobile ? 1000 / 30 : 1000 / 60,
        };
    }

    function buildPoints(count) {
        var points = [];
        var offset = 2 / count;
        var increment = Math.PI * (3 - Math.sqrt(5));

        for (var i = 0; i < count; i += 1) {
            var y = (i * offset - 1) + offset / 2;
            var radius = Math.sqrt(1 - y * y);
            var phi = i * increment;

            points.push({
                x: Math.cos(phi) * radius,
                y: y,
                z: Math.sin(phi) * radius,
            });
        }

        return points;
    }

    function initCanvas() {
        var canvas = document.createElement('canvas');
        canvas.className = 'terminal-earth';
        canvas.setAttribute('aria-hidden', 'true');
        document.body.appendChild(canvas);

        var ctx = canvas.getContext('2d');
        if (!ctx) {
            return;
        }

        var width = 0;
        var height = 0;
        var radius = 0;
        var dpr = 1;
        var rafId = null;
        var lastTime = 0;
        var rotation = 0;
        var points = [];
        var cfg = getConfig();
        var frameInterval = cfg.frameInterval;

        function resize() {
            cfg = getConfig();

            var maxSize = cfg.mobile ? 280 : 520;
            var minSize = cfg.mobile ? 200 : 320;
            var size = Math.min(window.innerWidth * (cfg.mobile ? 0.65 : 0.45), maxSize);
            size = Math.max(size, minSize);

            width = size;
            height = size;
            dpr = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = width * dpr;
            canvas.height = height * dpr;
            canvas.style.width = width + 'px';
            canvas.style.height = height + 'px';
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

            radius = size * cfg.radiusScale;
            points = buildPoints(cfg.pointCount);
            frameInterval = cfg.frameInterval;
        }

        function drawFrame(now) {
            var elapsed = now - lastTime;
            if (elapsed < frameInterval) {
                rafId = window.requestAnimationFrame(drawFrame);
                return;
            }
            lastTime = now - (elapsed % frameInterval);

            rotation += elapsed * cfg.rotationSpeed;

            ctx.clearRect(0, 0, width, height);
            ctx.save();
            ctx.translate(width / 2, height / 2);
            ctx.globalCompositeOperation = 'lighter';

            var cosY = Math.cos(rotation);
            var sinY = Math.sin(rotation);
            var tilt = 0.6;
            var cosT = Math.cos(tilt);
            var sinT = Math.sin(tilt);

            for (var i = 0; i < points.length; i += 1) {
                var point = points[i];
                var x = point.x;
                var z = point.z;
                var rx = x * cosY - z * sinY;
                var rz = x * sinY + z * cosY;
                var ry = point.y * cosT - rz * sinT;
                var rzz = point.y * sinT + rz * cosT;

                var depth = (rzz + cfg.depth) / (2 * cfg.depth);
                var scale = 0.6 + depth * 0.6;
                var px = rx * radius * scale;
                var py = ry * radius * scale;
                var alpha = 0.15 + depth * 0.8;
                var size = cfg.dotSize * scale;

                ctx.fillStyle = 'rgba(0, 255, 117, ' + alpha.toFixed(3) + ')';
                ctx.fillRect(px - size / 2, py - size / 2, size, size);
            }

            ctx.restore();
            rafId = window.requestAnimationFrame(drawFrame);
        }

        function drawStatic() {
            ctx.clearRect(0, 0, width, height);
            ctx.save();
            ctx.translate(width / 2, height / 2);
            ctx.globalCompositeOperation = 'lighter';

            var tilt = 0.6;
            var cosT = Math.cos(tilt);
            var sinT = Math.sin(tilt);

            for (var i = 0; i < points.length; i += 1) {
                var point = points[i];
                var ry = point.y * cosT - point.z * sinT;
                var rzz = point.y * sinT + point.z * cosT;

                var depth = (rzz + cfg.depth) / (2 * cfg.depth);
                var scale = 0.6 + depth * 0.6;
                var px = point.x * radius * scale;
                var py = ry * radius * scale;
                var alpha = 0.12 + depth * 0.7;
                var size = cfg.dotSize * scale;

                ctx.fillStyle = 'rgba(0, 255, 117, ' + alpha.toFixed(3) + ')';
                ctx.fillRect(px - size / 2, py - size / 2, size, size);
            }

            ctx.restore();
        }

        function start() {
            if (rafId) {
                window.cancelAnimationFrame(rafId);
            }
            lastTime = window.performance.now();
            rafId = window.requestAnimationFrame(drawFrame);
        }

        resize();

        if (prefersReducedMotion()) {
            drawStatic();
        } else {
            start();
        }

        var resizeTimer = null;
        window.addEventListener('resize', function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(function () {
                resize();
                if (prefersReducedMotion()) {
                    drawStatic();
                } else {
                    start();
                }
            }, 160);
        });

        document.addEventListener('visibilitychange', function () {
            if (prefersReducedMotion()) {
                return;
            }
            if (document.hidden) {
                if (rafId) {
                    window.cancelAnimationFrame(rafId);
                }
            } else {
                start();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initCanvas);
})();
