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
