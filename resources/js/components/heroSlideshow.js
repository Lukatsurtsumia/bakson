// Hero slideshow. The active progress bar's CSS animation times each slide: its animationend
// calls next(), and pausing the animation (hidden tab) pauses the slideshow.
export default ({ titles, start }) => ({
    titles,
    active: start,
    // Photos whose <img> has a src. Starts with the current and next one, so a phone doesn't
    // download every slide upfront.
    loaded: [start, (start + 1) % titles.length],
    pageHidden: false,
    reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)')
        .matches,
    swipeStart: null,

    init() {
        document.addEventListener('visibilitychange', () => {
            this.pageHidden = document.hidden;
        });
    },

    pad(number) {
        return String(number).padStart(2, '0');
    },

    isLoaded(index) {
        return this.loaded.includes(index);
    },

    go(index) {
        const count = this.titles.length;

        this.active = (index + count) % count;

        for (const preload of [this.active, (this.active + 1) % count]) {
            if (!this.loaded.includes(preload)) {
                this.loaded.push(preload);
            }
        }
    },

    next() {
        this.go(this.active + 1);
    },

    onPointerDown(event) {
        if (event.pointerType !== 'mouse') {
            this.swipeStart = { x: event.clientX, y: event.clientY };
        }
    },

    onPointerUp(event) {
        if (!this.swipeStart) {
            return;
        }

        const deltaX = event.clientX - this.swipeStart.x;
        const deltaY = event.clientY - this.swipeStart.y;
        this.swipeStart = null;

        if (
            Math.abs(deltaX) > 50 &&
            Math.abs(deltaX) > Math.abs(deltaY) * 1.5
        ) {
            this.go(this.active + (deltaX < 0 ? 1 : -1));
        }
    },
});
