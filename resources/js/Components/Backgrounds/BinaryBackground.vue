<script setup>
    import { onMounted, onBeforeUnmount, ref } from "vue";

    const props = defineProps({
        color: { type: String, default: "255,255,255" }, // RGB
        opacity: { type: Number, default: 0.12 },
        fontSize: { type: Number, default: 14 },
        density: { type: Number, default: 0.2 },
        fps: { type: Number, default: 12 },
    });

    // Canvas reference and related variables
    const canvas = ref(null);
    const wrapper = ref(null);
    let ctx = null;
    let raf = null;
    let cols = 0;
    let rows = 0;
    let grid = [];
    let cellW = 0;
    let cellH = 0;
    let visible = true;
    let mql = null;
    let resizeObserver = null;
    let intersectionObserver = null;
    let isIntersecting = true;

    // pause when page hidden
    const handleVisibility = () => {
        visible = !document.hidden;
        if (visible && !raf) {
            drawFrame.lastTime = performance.now();
            raf = requestAnimationFrame(drawFrame);
        }
    };

    // Utility function to return true with probability p
    function randChance(p) {
        return Math.random() < p;
    }

    // Resize canvas to fill its container
    function resize() {
        if (!canvas.value || !wrapper.value) return;
        const dpr = window.devicePixelRatio || 1;
        const rect = wrapper.value.getBoundingClientRect();
        
        if (rect.width === 0 || rect.height === 0) return;

        canvas.value.width = rect.width * dpr;
        canvas.value.height = rect.height * dpr;
        
        if (!ctx) {
            ctx = canvas.value.getContext("2d");
        }
        
        if (ctx) {
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            ctx.textBaseline = "top";
            ctx.font = `${props.fontSize}px monospace`;
        }
        rebuildGrid();
        redrawAll();
    }

    // Redraw all cells
    function redrawAll() {
        if (!ctx || !canvas.value) return;
        const dpr = window.devicePixelRatio || 1;
        const w = canvas.value.width / dpr;
        const h = canvas.value.height / dpr;
        
        ctx.clearRect(0, 0, w, h);
        ctx.globalAlpha = props.opacity;
        ctx.fillStyle = `rgb(${props.color})`;
        
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const idx = r * cols + c;
                const ch = grid[idx];
                if (!ch) continue;
                ctx.fillText(ch, c * cellW, r * cellH);
            }
        }
    }

    // Rebuild the grid based on current canvas size
    function rebuildGrid() {
        if (!canvas.value) return;
        const dpr = window.devicePixelRatio || 1;
        const w = canvas.value.width / dpr;
        const h = canvas.value.height / dpr;
        cols = Math.max(1, Math.floor(w / props.fontSize));
        rows = Math.max(1, Math.floor(h / props.fontSize));
        cellW = props.fontSize;
        cellH = props.fontSize;
        grid = new Array(cols * rows)
            .fill(null)
            .map(() =>
                randChance(props.density) ? (Math.random() < 0.5 ? "0" : "1") : null
            );
    }

    // Main draw loop
    function drawFrame(now) {
        if (!visible || !isIntersecting) {
            raf = null;
            return;
        }

        const frameInterval = 1000 / Math.max(1, props.fps);
        if (!drawFrame.lastTime) drawFrame.lastTime = now;
        const elapsed = now - drawFrame.lastTime;
        if (elapsed < frameInterval) {
            raf = requestAnimationFrame(drawFrame);
            return;
        }
        drawFrame.lastTime = now;

        const dpr = window.devicePixelRatio || 1;
        const w = canvas.value.width / dpr;
        const h = canvas.value.height / dpr;

        // only update a small random subset of cells per frame
        const total = cols * rows;
        const updates = Math.max(1, Math.floor(total * props.density * 0.03));

        for (let i = 0; i < updates; i++) {
            const idx = Math.floor(Math.random() * total);
            const old = grid[idx];
            const ch = randChance(props.density)
                ? Math.random() < 0.5
                    ? "0"
                    : "1"
                : null;
            if (old === ch) continue;
            grid[idx] = ch;
            const x = (idx % cols) * cellW;
            const y = Math.floor(idx / cols) * cellH;

            // clear cell area
            ctx.clearRect(x, y, cellW, cellH);
            if (ch) {
                // occasional brighten
                ctx.globalAlpha = randChance(0.02)
                    ? Math.min(0.22, props.opacity * 4)
                    : props.opacity;
                ctx.fillStyle = `rgb(${props.color})`;
                ctx.fillText(ch, x, y);
            }
        }

        raf = requestAnimationFrame(drawFrame);
    }

    // Start the animation
    function start() {
        if (!canvas.value) return;

        
        if (!ctx) {
            ctx = canvas.value.getContext("2d");
            if (ctx) {
                ctx.textBaseline = "top";
                ctx.font = `${props.fontSize}px monospace`;
            }
        }
        
        resize();
        drawFrame.lastTime = 0;
        if (!raf) raf = requestAnimationFrame(drawFrame);
    }

    // Lifecycle hooks
    onMounted(() => {
        document.addEventListener("visibilitychange", handleVisibility);
        
        // Use ResizeObserver for more reliable resizing
        if (window.ResizeObserver && wrapper.value) {
            resizeObserver = new ResizeObserver(() => {
                resize();
            });
            resizeObserver.observe(wrapper.value);
        } else {
            window.addEventListener("resize", resize);
        }

        // Stop rAF when off-screen
        if (window.IntersectionObserver && wrapper.value) {
            intersectionObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    const wasOffScreen = !isIntersecting;
                    isIntersecting = entry.isIntersecting;
                    if (isIntersecting && wasOffScreen && !raf) {
                        drawFrame.lastTime = performance.now();
                        raf = requestAnimationFrame(drawFrame);
                    }
                });
            }, { threshold: 0 });
            intersectionObserver.observe(wrapper.value);
        }
        
        start();
    });

    // Cleanup on unmount
    onBeforeUnmount(() => {
        if (raf) cancelAnimationFrame(raf);
        if (resizeObserver) {
            resizeObserver.disconnect();
        }
        if (intersectionObserver) {
            intersectionObserver.disconnect();
        }
        window.removeEventListener("resize", resize);
        document.removeEventListener("visibilitychange", handleVisibility);
        
    });
</script>

<template>
    <div ref="wrapper" class="binary-bg-wrapper">
        <canvas ref="canvas" class="binary-bg-canvas" />
    </div>
</template>

<style scoped>
    .binary-bg-wrapper {
        position: absolute;
        inset: 0;
        pointer-events: none;
    }

    .binary-bg-canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        display: block;
        z-index: 0;
        pointer-events: none;
        mix-blend-mode: screen;
    }
</style>
