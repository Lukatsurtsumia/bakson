// Fixed header: transparent over the hero, solid once the page scrolls or the phone menu opens.
export default () => ({
    scrolled: false,
    menuOpen: false,

    get solid() {
        return this.scrolled || this.menuOpen;
    },

    init() {
        this.onScroll();
    },

    onScroll() {
        this.scrolled = window.scrollY > 24;
    },
});
