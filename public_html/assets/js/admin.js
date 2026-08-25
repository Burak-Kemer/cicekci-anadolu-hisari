document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('adminMenuToggle');
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('adminSidebarBackdrop');

    if (!toggle || !sidebar) {
        return;
    }

    function openSidebar() {
        sidebar.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
        if (backdrop) {
            backdrop.classList.add('is-open');
        }
        document.documentElement.classList.add('admin-scroll-lock');
    }

    function closeSidebar() {
        sidebar.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        if (backdrop) {
            backdrop.classList.remove('is-open');
        }
        document.documentElement.classList.remove('admin-scroll-lock');
    }

    toggle.addEventListener('click', () => {
        if (sidebar.classList.contains('is-open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    document.addEventListener('click', (event) => {
        if (!sidebar.classList.contains('is-open')) {
            return;
        }
        if (sidebar.contains(event.target) || toggle.contains(event.target)) {
            return;
        }
        closeSidebar();
    });
});

// Ürün formu: "Sabit Fiyat" / "Fiyat İçin İletişime Geçin" seçimine göre
// fiyat alanını görsel olarak devre dışı bırakır. Sunucu tarafı doğrulama
// zaten zorunludur — bu sadece kullanıcı deneyimini iyileştirir.
document.addEventListener('DOMContentLoaded', () => {
    const priceRadios = document.querySelectorAll('input[name="price_status"]');
    const priceField = document.querySelector('input[name="price"]');

    if (!priceRadios.length || !priceField) {
        return;
    }

    const syncPriceField = () => {
        const isContact = document.querySelector('input[name="price_status"]:checked')?.value === 'contact';
        priceField.disabled = isContact;
        if (isContact) {
            priceField.value = '';
        }
    };

    priceRadios.forEach((radio) => radio.addEventListener('change', syncPriceField));
    syncPriceField();
});
