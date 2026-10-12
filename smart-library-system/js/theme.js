(() => {
    const storageKey = "smart-library-theme";
    const root = document.documentElement;
    const preferredTheme = () => {
        const saved = localStorage.getItem(storageKey);
        return saved === "light" || saved === "dark"
            ? saved
            : (matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light");
    };
    const setTheme = (theme) => {
        root.dataset.theme = theme;
        localStorage.setItem(storageKey, theme);
        document.querySelectorAll("[data-theme-toggle]").forEach((button) => {
            const dark = theme === "dark";
            button.setAttribute("aria-pressed", String(dark));
            button.setAttribute("aria-label", dark ? "Switch to light mode" : "Switch to dark mode");
            const label = button.querySelector("[data-theme-label]");
            const icon = button.querySelector("[data-theme-icon]");
            if (label) label.textContent = dark ? "Light mode" : "Dark mode";
            if (icon) icon.textContent = dark ? "☀" : "☾";
        });
    };
    setTheme(preferredTheme());
    document.addEventListener("click", (event) => {
        if (event.target.closest("[data-theme-toggle]")) {
            setTheme(root.dataset.theme === "dark" ? "light" : "dark");
        }
    });
})();
