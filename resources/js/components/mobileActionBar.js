// Phone "Appeler / Devis" bar: shown once the hero is scrolled past, hidden while the contact
// section is on screen so it never covers the form.
export default () => ({
    pastHero: false,
    contactVisible: false,

    get visible() {
        return this.pastHero && !this.contactVisible;
    },

    init() {
        this.onScroll();

        const contactSection = document.getElementById('contact');

        if (contactSection) {
            new IntersectionObserver(
                ([entry]) => {
                    this.contactVisible = entry.isIntersecting;
                },
                { rootMargin: '0px 0px -30% 0px' },
            ).observe(contactSection);
        }
    },

    onScroll() {
        this.pastHero = window.scrollY > window.innerHeight * 0.6;
    },
});
