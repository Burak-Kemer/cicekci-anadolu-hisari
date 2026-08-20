document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('adminMenuToggle');
    const sidebar = document.getElementById('adminSidebar');

    if (!toggle || !sidebar) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', (event) => {
        if (!sidebar.classList.contains('is-open')) {
            return;
        }
        if (sidebar.contains(event.target) || toggle.contains(event.target)) {
            return;
        }
        sidebar.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
    });
});
