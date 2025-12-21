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
            spacing: mobile ? 120 : 160,
            jitter: mobile ? 12 : 20,
            lineCount: mobile ? 8 : 14,
            pulsePerLine: mobile ? 1 : 2,
            maxPulses: mobile ? 14 : 28,
            frameInterval: mobile ? 1000 / 40 : 1000 / 60,
        };
    }

    function buildGrid(width, height, cfg) {
        var cols = Math.ceil(width / cfg.spacing) + 1;
        var rows = Math.ceil(height / cfg.spacing) + 1;
        var grid = [];

        for (var r = 0; r < rows; r += 1) {
            var row = [];
            for (var c = 0; c < cols; c += 1) {
                var x = c * cfg.spacing + (Math.random() - 0.5) * cfg.jitter;
                var y = r * cfg.spacing + (Math.random() - 0.5) * cfg.jitter;
                row.push({ x: x, y: y });
            }
            grid.push(row);
        }

        return { grid: grid, rows: rows, cols: cols };
    }

    function buildPath(points) {
        var segments = [];
        var total = 0;

        for (var i = 0; i < points.length - 1; i += 1) {
            var start = points[i];
            var end = points[i + 1];
            var dx = end.x - start.x;
            var dy = end.y - start.y;
            var length = Math.sqrt(dx * dx + dy * dy);
            if (!length) {
                continue;
            }
            segments.push({
                x1: start.x,
                y1: start.y,
                x2: end.x,
                y2: end.y,
                length: length,
                dx: dx / length,
                dy: dy / length,
            });
            total += length;
        }

        return { points: points, segments: segments, total: total };
    }

    function createPath(gridData) {
        var grid = gridData.grid;
        var rows = gridData.rows;
        var cols = gridData.cols;
        var startRow;
        var startCol;
        var endRow;
        var endCol;
        var attempts = 0;

        do {
            startRow = Math.floor(Math.random() * rows);
            startCol = Math.floor(Math.random() * cols);
            endRow = Math.floor(Math.random() * rows);
            endCol = Math.floor(Math.random() * cols);
            attempts += 1;
        } while (
            attempts < 10 &&
            (startRow === endRow && startCol === endCol ||
                Math.abs(startRow - endRow) + Math.abs(startCol - endCol) < 2)
        );

        var start = grid[startRow][startCol];
        var end = grid[endRow][endCol];
        var points = [start];

        if (Math.random() > 0.5) {
            points.push(grid[startRow][endCol]);
        } else {
            points.push(grid[endRow][startCol]);
        }

        points.push(end);

        return buildPath(points);
    }

    function pointAt(path, t) {
        if (!path.total) {
            return null;
        }
        var distance = t * path.total;
        for (var i = 0; i < path.segments.length; i += 1) {
            var seg = path.segments[i];
            if (distance <= seg.length) {
                return {
                    x: seg.x1 + seg.dx * distance,
                    y: seg.y1 + seg.dy * distance,
                    dx: seg.dx,
                    dy: seg.dy,
                };
            }
            distance -= seg.length;
        }
        var last = path.segments[path.segments.length - 1];
        return last
            ? { x: last.x2, y: last.y2, dx: last.dx, dy: last.dy }
            : null;
    }

    function buildPulses(paths, cfg) {
        var pulses = [];
        for (var i = 0; i < paths.length; i += 1) {
            var perLine = cfg.pulsePerLine + (Math.random() > 0.7 ? 1 : 0);
            for (var j = 0; j < perLine; j += 1) {
                pulses.push({
                    pathIndex: i,
                    t: Math.random(),
                    speed: (Math.random() * 0.002 + 0.0016) * (cfg.mobile ? 0.8 : 1),
                    size: Math.random() * 1.2 + 1.6,
                    color: Math.random() > 0.7 ? 'rgba(0, 255, 213, 0.9)' : 'rgba(0, 229, 255, 0.85)',
                });
            }
        }
        return pulses.slice(0, cfg.maxPulses);
    }

    function initCanvas() {
        if (prefersReducedMotion()) {
            return;
        }

        var canvas = document.createElement('canvas');
        canvas.className = 'jarvis-circuit';
        canvas.setAttribute('aria-hidden', 'true');
        document.body.appendChild(canvas);

        var ctx = canvas.getContext('2d');
        if (!ctx) {
            return;
        }

        var width = 0;
        var height = 0;
        var dpr = 1;
        var rafId = null;
        var lastTime = 0;
        var paths = [];
        var pulses = [];
        var frameInterval = 1000 / 60;

        function resize() {
            var cfg = getConfig();
            width = window.innerWidth;
            height = window.innerHeight;
            dpr = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = width * dpr;
            canvas.height = height * dpr;
            canvas.style.width = width + 'px';
            canvas.style.height = height + 'px';
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            frameInterval = cfg.frameInterval;

            var gridData = buildGrid(width, height, cfg);
            paths = [];
            for (var i = 0; i < cfg.lineCount; i += 1) {
                paths.push(createPath(gridData));
            }
            pulses = buildPulses(paths, cfg);
        }

        function draw(now) {
            var elapsed = now - lastTime;
            if (elapsed < frameInterval) {
                rafId = window.requestAnimationFrame(draw);
                return;
            }
            lastTime = now - (elapsed % frameInterval);
            var delta = Math.min(elapsed / 16, 1.8);

            ctx.clearRect(0, 0, width, height);
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';

            ctx.strokeStyle = 'rgba(0, 229, 255, 0.18)';
            ctx.lineWidth = 1;
            ctx.beginPath();
            for (var i = 0; i < paths.length; i += 1) {
                var path = paths[i];
                if (!path.points.length) {
                    continue;
                }
                ctx.moveTo(path.points[0].x, path.points[0].y);
                for (var p = 1; p < path.points.length; p += 1) {
                    ctx.lineTo(path.points[p].x, path.points[p].y);
                }
            }
            ctx.stroke();

            ctx.fillStyle = 'rgba(0, 229, 255, 0.35)';
            for (var n = 0; n < paths.length; n += 1) {
                var points = paths[n].points;
                for (var k = 0; k < points.length; k += 1) {
                    var node = points[k];
                    ctx.beginPath();
                    ctx.arc(node.x, node.y, 2.2, 0, Math.PI * 2);
                    ctx.fill();
                }
            }

            ctx.save();
            ctx.globalCompositeOperation = 'lighter';
            for (var j = 0; j < pulses.length; j += 1) {
                var pulse = pulses[j];
                pulse.t = (pulse.t + pulse.speed * delta) % 1;
                var target = pointAt(paths[pulse.pathIndex], pulse.t);
                if (!target) {
                    continue;
                }

                var trail = 10 + pulse.size * 4;
                ctx.strokeStyle = 'rgba(0, 229, 255, 0.5)';
                ctx.lineWidth = 1.6;
                ctx.beginPath();
                ctx.moveTo(target.x - target.dx * trail, target.y - target.dy * trail);
                ctx.lineTo(target.x + target.dx * trail * 0.3, target.y + target.dy * trail * 0.3);
                ctx.stroke();

                ctx.fillStyle = pulse.color;
                ctx.beginPath();
                ctx.arc(target.x, target.y, pulse.size, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = 'rgba(0, 255, 213, 0.15)';
                ctx.beginPath();
                ctx.arc(target.x, target.y, pulse.size * 3, 0, Math.PI * 2);
                ctx.fill();
            }
            ctx.restore();

            rafId = window.requestAnimationFrame(draw);
        }

        function start() {
            if (rafId) {
                window.cancelAnimationFrame(rafId);
            }
            lastTime = window.performance.now();
            rafId = window.requestAnimationFrame(draw);
        }

        resize();
        start();

        var resizeTimer = null;
        window.addEventListener('resize', function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(function () {
                resize();
                start();
            }, 160);
        });

        document.addEventListener('visibilitychange', function () {
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
