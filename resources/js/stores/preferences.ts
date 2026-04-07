import { i18n } from '@/config/i18n';
import { navigatorLanguage } from '@/lib/utils';
import { useColorMode, useStorage } from '@vueuse/core';
import { defineStore } from 'pinia';
import { ref, watch } from 'vue';

function preferredTheme() {
  if (window.matchMedia('(prefers-contrast: more)').matches) return 'highContrast';
  if (window.matchMedia('prefers-color-scheme: dark)').matches) return 'dark';
  return 'light';
}

export const usePreferencesStore = defineStore('preferences', () => {
  const locale = useStorage<string>('locale', navigatorLanguage() ?? 'pl');
  const fontSize = useStorage<'normal' | 'large'>('font-size', 'normal');
  const colorMode = useColorMode({
    modes: {
      highContrast: 'high-contrast',
    },
    initialValue: preferredTheme(),
    storageKey: 'color-mode',
  });
  const mobileNavOpen = ref(false);

  watch(i18n.global.locale, (val) => {
    locale.value = val;
  });

  return { fontSize, locale, colorMode, mobileNavOpen };
});
