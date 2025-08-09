import createI18n from "@azayaka-frontend/i18n";

import enTranslations from "@/i18n/translations/en.json";
import plTranslations from "@/i18n/translations/pl.json";

const i18n = createI18n({
    navigatorLanguage: navigator.language,
    messages: {
        pl: plTranslations,
        en: enTranslations,
    },
});

export default i18n;
