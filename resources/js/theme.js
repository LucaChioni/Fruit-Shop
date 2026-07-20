const themeStorageKey = 'fruit_shop_theme';

export function getPreferredTheme() {
    try {
        const storedTheme = window.localStorage.getItem(themeStorageKey);

        if (storedTheme === 'dark' || storedTheme === 'light') {
            return storedTheme;
        }
    } catch {
        // Fall back to the system preference when localStorage is unavailable.
    }

    return window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

export function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.style.colorScheme = theme;
}

export function saveTheme(theme) {
    try {
        window.localStorage.setItem(themeStorageKey, theme);
    } catch {
        // The visual change still applies for the current page view.
    }

    applyTheme(theme);
}
