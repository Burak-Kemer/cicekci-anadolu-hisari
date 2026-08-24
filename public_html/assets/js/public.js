document.addEventListener('DOMContentLoaded', () => {
    initMobileDrawer();
    initProductGalleryDots();
    scrollActiveChipIntoView();
    initScrollReveal();
});

function trapFocus(container) {
    const focusable = container.querySelectorAll(
        'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    if (focusable.length === 0) {
        return;
    }
    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    container.addEventListener('keydown', (event) => {
        if (event.key !== 'Tab') {
            return;
        }
        if (event.shiftKey) {
            if (document.activeElement === first) {
                event.preventDefault();
                last.focus();
            }
        } else if (document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });
}

function initMobileDrawer() {
    const toggle = document.getElementById('menuToggle');
    const drawer = document.getElementById('mobileDrawer');
    const closeBtn = document.getElementById('drawerClose');
    const backdrop = document.getElementById('drawerBackdrop');

    if (!toggle || !drawer) {
        return;
    }

    let lastFocused = null;

    function openDrawer() {
        lastFocused = document.activeElement;
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        toggle.setAttribute('aria-expanded', 'true');
        document.documentElement.classList.add('drawer-open');
        if (closeBtn) {
            closeBtn.focus();
        }
    }

    function closeDrawer() {
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        toggle.setAttribute('aria-expanded', 'false');
        document.documentElement.classList.remove('drawer-open');
        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }
    }

    toggle.addEventListener('click', () => {
        if (drawer.classList.contains('is-open')) {
            closeDrawer();
        } else {
            openDrawer();
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeDrawer);
    }
    if (backdrop) {
        backdrop.addEventListener('click', closeDrawer);
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && drawer.classList.contains('is-open')) {
            closeDrawer();
        }
    });

    const panel = drawer.querySelector('.mobile-drawer__panel');
    if (panel) {
        trapFocus(panel);
    }
}

function initProductGalleryDots() {
    const track = document.getElementById('productGalleryTrack');
    const dotsContainer = document.getElementById('productGalleryDots');
    if (!track || !dotsContainer || !('IntersectionObserver' in window)) {
        return;
    }

    const slides = track.querySelectorAll('.product-gallery__slide');
    const dots = dotsContainer.querySelectorAll('.product-gallery__dot');
    if (slides.length !== dots.length || slides.length === 0) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting && entry.intersectionRatio > 0.6) {
                    const index = Array.from(slides).indexOf(entry.target);
                    dots.forEach((dot, i) => dot.classList.toggle('is-active', i === index));
                }
            });
        },
        { root: track, threshold: [0.6] }
    );

    slides.forEach((slide) => observer.observe(slide));
}

function scrollActiveChipIntoView() {
    const activeChip = document.querySelector('.category-chip.is-active');
    if (!activeChip) {
        return;
    }
    activeChip.scrollIntoView({ inline: 'center', block: 'nearest' });
}

function initScrollReveal() {
    if (
        window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
        !('IntersectionObserver' in window)
    ) {
        return;
    }

    const selectors = '.product-card, .category-tile, .feature-list__item, .split__text, .split__accent, .split__media, .about-photo, .contact-card';
    const elements = document.querySelectorAll(selectors);
    if (elements.length === 0) {
        return;
    }

    elements.forEach((el) => el.setAttribute('data-reveal', ''));

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
    );

    elements.forEach((el) => observer.observe(el));
}
