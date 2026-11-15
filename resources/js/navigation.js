let headerScrollHandler = null;
let activeDialog = null;

export function initNavigation() {
    const header = document.querySelector('[data-site-header]');
    const dialog = document.querySelector('[data-mobile-menu]');
    const openButton = document.querySelector('[data-menu-open]');
    const closeButton = document.querySelector('[data-menu-close]');

    if (headerScrollHandler) {
        window.removeEventListener('scroll', headerScrollHandler);
        headerScrollHandler = null;
    }

    if (header) {
        const updateHeader = () => {
            header.classList.toggle('is-scrolled', window.scrollY > 24);
        };

        headerScrollHandler = updateHeader;
        updateHeader();
        window.addEventListener('scroll', updateHeader, { passive: true });
    }

    if (!dialog || !openButton) {
        return;
    }

    activeDialog = dialog;

    const closeMenu = () => {
        if (dialog.open) {
            dialog.close();
        }

        openButton.setAttribute('aria-expanded', 'false');
    };

    openButton.addEventListener('click', () => {
        dialog.showModal();
        document.documentElement.classList.add('menu-open');
        openButton.setAttribute('aria-expanded', 'true');
        closeButton?.focus();
    });

    closeButton?.addEventListener('click', closeMenu);

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            closeMenu();
        }
    });

    dialog.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    dialog.addEventListener('close', () => {
        document.documentElement.classList.remove('menu-open');
        openButton.setAttribute('aria-expanded', 'false');
    });
}

export function cleanupNavigation() {
    if (headerScrollHandler) {
        window.removeEventListener('scroll', headerScrollHandler);
        headerScrollHandler = null;
    }

    if (activeDialog?.open) {
        activeDialog.close();
    }

    activeDialog = null;
    document.documentElement.classList.remove('menu-open');
}
