const STORAGE_KEY = 'dd-theme';

let systemListenerBound = false;

function systemPrefersLight() {
    return window.matchMedia('(prefers-color-scheme: light)').matches;
}

function applyTheme(theme, persist = false) {
    const isDark = theme === 'dark';

    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.dataset.theme = isDark ? 'dark' : 'light';

    if (persist) {
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch {
            // Storage can be unavailable in private contexts; the theme still applies.
        }
    }

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const nextTheme = isDark ? 'light' : 'dark';
        button.setAttribute('aria-label', `Switch to ${nextTheme} mode`);
        button.setAttribute('title', `Switch to ${nextTheme} mode`);
        button.setAttribute('aria-pressed', String(isDark));

        if (button.dataset.themeBound === 'true') {
            return;
        }

        button.dataset.themeBound = 'true';
        button.addEventListener('click', () => {
            const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            applyTheme(next, true);
        });
    });
}

function storedTheme() {
    try {
        const value = localStorage.getItem(STORAGE_KEY);

        return value === 'dark' || value === 'light' ? value : null;
    } catch {
        return null;
    }
}

export function initTheme() {
    applyTheme(storedTheme() ?? (document.documentElement.dataset.theme === 'light' ? 'light' : 'dark'));

    if (systemListenerBound) {
        return;
    }

    systemListenerBound = true;

    window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', () => {
        if (!storedTheme()) {
            applyTheme(systemPrefersLight() ? 'light' : 'dark');
        }
    });
}
