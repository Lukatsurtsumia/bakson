// Before/after section (resources/views/home/before-after.blade.php): the scroll-snap carousel of
// sliders and the fullscreen viewer they open.
export default ({ titles, services }) => ({
    titles,
    services,
    count: titles.length,

    activeIndex: 0,
    scrollProgress: 0,
    atStart: true,
    atEnd: false,

    viewerOpen: false,
    viewerIndex: 0,
    ownsHistoryEntry: false,
    focusBeforeViewer: null,

    init() {
        this.onTrackScroll();

        this.onPopstate = () => {
            // The phone's back button: close the viewer instead of leaving the page.
            if (this.ownsHistoryEntry) {
                this.ownsHistoryEntry = false;
                this.teardownViewer();
            }
        };

        this.onViewerKeydown = (event) => {
            if (event.key === 'Escape') {
                this.closeViewer();
            } else if (event.key === 'ArrowRight') {
                this.viewerGoTo(this.viewerIndex + 1);
            } else if (event.key === 'ArrowLeft') {
                this.viewerGoTo(this.viewerIndex - 1);
            }
        };
    },

    pad(number) {
        return String(number).padStart(2, '0');
    },

    cardStep() {
        const track = this.$refs.track;
        const gap = parseFloat(getComputedStyle(track).columnGap) || 0;

        return track.children[0].offsetWidth + gap;
    },

    onTrackScroll() {
        const { scrollLeft, scrollWidth, clientWidth } = this.$refs.track;

        this.activeIndex = Math.min(
            this.count - 1,
            Math.round(scrollLeft / this.cardStep()),
        );
        this.scrollProgress = (scrollLeft + clientWidth) / scrollWidth;
        this.atStart = scrollLeft < 4;
        this.atEnd = scrollLeft + clientWidth >= scrollWidth - 4;
    },

    scrollByCard(direction) {
        this.$refs.track.scrollBy({
            left: direction * this.cardStep(),
            behavior: 'smooth',
        });
    },

    openViewer(index) {
        this.viewerIndex = index;
        this.viewerOpen = true;
        this.focusBeforeViewer = document.activeElement;
        document.documentElement.classList.add('overflow-hidden');

        // An extra history entry lets the phone's back button close the viewer.
        history.pushState({ beforeAfterViewer: true }, '');
        this.ownsHistoryEntry = true;
        window.addEventListener('popstate', this.onPopstate);
        window.addEventListener('keydown', this.onViewerKeydown);

        this.$nextTick(() => {
            const track = this.$refs.viewerTrack;
            track.scrollTo({
                left: index * track.clientWidth,
                behavior: 'instant',
            });
            this.$refs.closeButton.focus({ preventScroll: true });
        });
    },

    closeViewer() {
        if (this.ownsHistoryEntry) {
            this.ownsHistoryEntry = false;
            history.back();
        }

        this.teardownViewer();
    },

    teardownViewer() {
        this.viewerOpen = false;
        document.documentElement.classList.remove('overflow-hidden');
        window.removeEventListener('popstate', this.onPopstate);
        window.removeEventListener('keydown', this.onViewerKeydown);
        this.focusBeforeViewer?.focus?.({ preventScroll: true });
    },

    viewerGoTo(index) {
        if (index >= 0 && index < this.count) {
            const track = this.$refs.viewerTrack;
            track.scrollTo({
                left: index * track.clientWidth,
                behavior: 'smooth',
            });
        }
    },

    onViewerScroll() {
        const track = this.$refs.viewerTrack;

        this.viewerIndex = Math.round(track.scrollLeft / track.clientWidth);
    },
});
