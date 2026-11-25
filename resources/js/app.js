import "./bootstrap";
import { createApp } from "vue";
import { createI18n } from "vue-i18n";

// Basic i18n setup with default locale
import en from "./locales/en.json";
import nl from "./locales/nl.json";

const messages = {
    en,
    nl,
};

const i18n = createI18n({
    locale: localStorage.getItem("locale") || "en",
    fallbackLocale: "en",
    messages,
});

const appElement = document.getElementById("app");
if (!appElement) {
    throw new Error('Missing #app mount point in the page shell');
}
const pageName = appElement.dataset.page;
const isAuthenticated = appElement.dataset.auth === "true";
const pageProps = JSON.parse(appElement.dataset.props || "{}");

// PWA support removed: no service worker registration or manifest injection

const pages = import.meta.glob("./Pages/**/*.vue");
const pagePath = `./Pages/${pageName}.vue`;

if (pages[pagePath]) {
    pages[pagePath]().then((module) => {
        const app = createApp(module.default, {
            auth: isAuthenticated,
            ...pageProps,
        });
        app.provide("auth", isAuthenticated);
        app.use(i18n);
        app.mount("#app");
    });
} else {
    console.error(`Page component not found: ${pagePath}`);
}

console.log(
    "%c   _____ _   _ ________      _______  ________      __\n  / ____| \\ | |  ____\\ \\    / /  __ \\|  ____\\ \\    / /\n | (___ |  \\| | |__   \\ \\  / /| |  | | |__   \\ \\  / / \n  \\___ \\| . ` |  __|   \\ \\/ / | |  | |  __|   \\ \\/ /  \n  ____) | |\\  | |____   \\  /  | |__| | |____   \\  /   \n |_____/|_| \\_|______|   \\/   |_____/|______|   \\/    \n                                                      \n",
    "color: #00ffd5; font-weight: bold;"
);