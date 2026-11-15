export function initLightbox() {
    const dialog = document.querySelector('[data-lightbox]');

    if (!dialog) {
        return;
    }

    const image = dialog.querySelector('[data-lightbox-image]');
    const caption = dialog.querySelector('[data-lightbox-caption]');
    const counter = dialog.querySelector('[data-lightbox-counter]');
    const closeButton = dialog.querySelector('[data-lightbox-close]');
    const prevButton = dialog.querySelector('[data-lightbox-prev]');
    const nextButton = dialog.querySelector('[data-lightbox-next]');
    let items = [];
    let activeIndex = 0;

    const render = () => {
        const item = items[activeIndex];

        if (!item) {
            return;
        }

        image.src = item.dataset.src;
        image.alt = item.dataset.alt || item.dataset.caption || '';
        caption.textContent = item.dataset.caption || item.dataset.alt || '';
        counter.textContent = `${activeIndex + 1} / ${items.length}`;
        prevButton.disabled = items.length < 2;
        nextButton.disabled = items.length < 2;
    };

    const open = (item) => {
        const group = item.dataset.lightboxGroup || 'default';
        const selector = `[data-lightbox-group="${window.CSS?.escape ? window.CSS.escape(group) : group}"]`;

        items = Array.from(document.querySelectorAll(selector));
        activeIndex = Math.max(0, items.indexOf(item));
        render();
        dialog.showModal();
    };

    const step = (direction) => {
        if (items.length < 2) {
            return;
        }

        activeIndex = (activeIndex + direction + items.length) % items.length;
        render();
    };

    document.querySelectorAll('[data-lightbox-item]').forEach((item) => {
        item.addEventListener('click', () => open(item));
    });

    closeButton?.addEventListener('click', () => dialog.close());
    prevButton?.addEventListener('click', () => step(-1));
    nextButton?.addEventListener('click', () => step(1));

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });

    dialog.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') {
            step(-1);
        }

        if (event.key === 'ArrowRight') {
            step(1);
        }
    });
}
