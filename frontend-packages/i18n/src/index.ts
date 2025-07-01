import { createI18n } from "vue-i18n";

import MessagesEN from "#i18n/common-translations/en.json";
import MessagesPL from "#i18n/common-translations/pl.json";
import DatetimeFormatsEN from "#i18n/datetime-formats/en.json";
import DatetimeFormatsPL from "#i18n/datetime-formats/pl.json";

export default ({
    messages,
    navigatorLanguage,
}: {
    messages: { pl: Record<string, unknown>; en: Record<string, unknown> };
    navigatorLanguage: string;
}) =>
    // @ts-expect-error nie zesraj się eslincie
    createI18n({
        locale: navigatorLanguage.split("-")[0],
        fallbackLocale: "pl",
        messages: {
            pl: { ...MessagesPL, ...messages["pl"] },
            en: { ...MessagesEN, ...messages["en"] },
        },
        datetimeFormats: {
            pl: DatetimeFormatsPL,
            en: DatetimeFormatsEN,
        },
        legacy: false,
    });

export { default as syncLocaleWithStore } from "./composables/syncLocaleWithStore";
