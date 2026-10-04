// Drag-to-compare photo (resources/views/components/before-after-slider.blade.php).
//
// A finger only drags from the handle, so swipes across the photo still scroll the page and the
// carousels; a mouse can drag from anywhere. A tap without movement dispatches
// `before-after-open` on the page, or moves the handle there in the fullscreen viewer.
export default ({ index, revealOnTap, hint }) => ({
    position: 50,
    dragging: false,
    gliding: false,
    gesture: null,
    hintTimers: [],
    hintObserver: null,

    init() {
        const prefersReducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)',
        ).matches;

        if (!hint || prefersReducedMotion) {
            return;
        }

        // Nudge the handle once when it first scrolls into view, to show it can be dragged.
        this.hintObserver = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    this.stopHint();
                    [72, 30, 50].forEach((percent, step) => {
                        this.hintTimers.push(
                            setTimeout(
                                () => this.glideTo(percent),
                                250 + step * 650,
                            ),
                        );
                    });
                }
            },
            { threshold: 0.6 },
        );
        this.hintObserver.observe(this.$el);
    },

    destroy() {
        this.stopHint();
    },

    stopHint() {
        this.hintTimers.forEach((timer) => clearTimeout(timer));
        this.hintTimers = [];
        this.hintObserver?.disconnect();
        this.hintObserver = null;
    },

    percentAt(clientX) {
        const rect = this.$el.getBoundingClientRect();

        return Math.min(
            100,
            Math.max(0, ((clientX - rect.left) / rect.width) * 100),
        );
    },

    glideTo(percent) {
        this.gliding = true;
        this.position = Math.min(100, Math.max(0, percent));
    },

    beginDrag(event) {
        this.gesture.isDragging = true;
        this.dragging = true;
        this.gliding = false;
        this.$el.setPointerCapture(event.pointerId);
    },

    onPointerDown(event) {
        if (event.pointerType === 'mouse' && event.button !== 0) {
            return;
        }

        this.stopHint();

        const startedOnHandle = Boolean(event.target.closest('[data-handle]'));

        this.gesture = {
            pointerId: event.pointerId,
            startX: event.clientX,
            startY: event.clientY,
            startedOnHandle,
            hasMoved: false,
            isDragging: false,
            canDrag: startedOnHandle || event.pointerType === 'mouse',
        };

        if (startedOnHandle) {
            this.beginDrag(event);
        }
    },

    onPointerMove(event) {
        const gesture = this.gesture;

        if (!gesture || event.pointerId !== gesture.pointerId) {
            return;
        }

        const deltaX = event.clientX - gesture.startX;
        const deltaY = event.clientY - gesture.startY;

        if (!gesture.hasMoved && Math.hypot(deltaX, deltaY) > 6) {
            gesture.hasMoved = true;

            if (
                !gesture.isDragging &&
                gesture.canDrag &&
                Math.abs(deltaX) >= Math.abs(deltaY)
            ) {
                this.beginDrag(event);
            }
        }

        if (gesture.isDragging) {
            this.position = this.percentAt(event.clientX);
        }
    },

    onPointerUp(event) {
        const gesture = this.gesture;

        if (!gesture || event.pointerId !== gesture.pointerId) {
            return;
        }

        const wasTap = !gesture.hasMoved && !gesture.startedOnHandle;
        this.endGesture();

        if (!wasTap) {
            return;
        }

        if (revealOnTap) {
            this.glideTo(this.percentAt(event.clientX));
        } else {
            this.$dispatch('before-after-open', { index });
        }
    },

    endGesture() {
        this.gesture = null;
        this.dragging = false;
    },

    onHandleKeydown(event) {
        const step = event.shiftKey ? 20 : 5;
        const targets = {
            ArrowLeft: this.position - step,
            ArrowDown: this.position - step,
            ArrowRight: this.position + step,
            ArrowUp: this.position + step,
            Home: 0,
            End: 100,
        };

        if (event.key in targets) {
            event.preventDefault();
            event.stopPropagation();
            this.stopHint();
            this.glideTo(targets[event.key]);
        }
    },
});
