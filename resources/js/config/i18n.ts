import DatetimeEN from '@/i18n/datetime/en.json';
import DatetimePL from '@/i18n/datetime/pl.json';
import MessagesEN from '@/i18n/messages/en.json';
import MessagesPL from '@/i18n/messages/pl.json';
import { usePreferencesStore } from '@/stores/preferences';
import { createI18n, type IntlDateTimeFormats } from 'vue-i18n';

export const i18n = createI18n<[typeof MessagesEN], 'pl' | 'en', false>({
  legacy: false,
  locale: 'pl',
  fallbackLocale: 'pl',
  messages: {
    pl: MessagesPL,
    en: MessagesEN,
  },
  pluralRules: {
    pl: (choice) => {
      if (choice === 0) return 2;
      if (choice === 1) return 0;
      const teen = choice > 10 && choice < 20;
      const lastDigit = choice % 10;
      if (!teen && (lastDigit >= 2 && lastDigit <= 4)) return 1;
      return 2;
    }
  },
  datetimeFormats: {
    pl: DatetimePL as unknown as IntlDateTimeFormats,
    en: DatetimeEN as unknown as IntlDateTimeFormats,
  },
});

export function loadI18nLocale() {
  const preferencesStore = usePreferencesStore();
  i18n.global.locale.value = preferencesStore.locale as 'en' | 'pl';
}
