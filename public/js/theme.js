(function () {
    function applyTheme(theme) {
        const root = document.documentElement;

        if (theme === 'dark') {
            root.classList.add('dark');
            root.setAttribute('data-theme', 'dark');
        } else {
            root.classList.remove('dark');
            root.setAttribute('data-theme', 'light');
        }
    }

    function getPreferredTheme() {
        const saved = localStorage.getItem('theme');

        if (saved === 'dark' || saved === 'light') {
            return saved;
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    window.crmTheme = {
        apply: applyTheme,
        current: getPreferredTheme,
        toggle: function () {
            const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem('theme', next);
            applyTheme(next);
            syncButtons();
        }
    };

    function syncButtons() {
        const isDark = document.documentElement.classList.contains('dark');
        const buttons = document.querySelectorAll('[data-theme-toggle]');

        buttons.forEach(function (button) {
            button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            button.setAttribute('title', isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');

            const lightLabel = button.querySelector('[data-theme-label-light]');
            const darkLabel = button.querySelector('[data-theme-label-dark]');

            if (lightLabel) {
                lightLabel.hidden = isDark;
            }

            if (darkLabel) {
                darkLabel.hidden = !isDark;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        applyTheme(getPreferredTheme());
        syncButtons();

        document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                window.crmTheme.toggle();
            });
        });

        const media = window.matchMedia('(prefers-color-scheme: dark)');
        if (typeof media.addEventListener === 'function') {
            media.addEventListener('change', function () {
                if (!localStorage.getItem('theme')) {
                    applyTheme(getPreferredTheme());
                    syncButtons();
                }
            });
        }
    });
})();