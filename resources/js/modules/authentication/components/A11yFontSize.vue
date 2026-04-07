<script setup lang="ts">
import { usePreferencesStore } from '@/stores/preferences';
import { useI18n } from 'vue-i18n';

const preferencesStore = usePreferencesStore();
const { t } = useI18n();
</script>

<template>
  <fieldset class="flex items-end gap-1.5" aria-hidden="true">
    <legend class="sr-only">{{ t('preferences.fontSize') }}</legend>
    <label
      class="rounded-sm text-[1.75rem]/10 cursor-pointer transition-colors"
      :class="{
        'bg-primary text-primary-foreground': preferencesStore.fontSize === 'large',
      }"
    >
      <span class="p-1" :aria-label="t('preferences.fontSizes.normal')">A</span>
      <input
        type="radio"
        name="font-size"
        value="large"
        class="sr-only"
        v-model="preferencesStore.fontSize"
      />
    </label>
    <label
      class="rounded-sm text-[1.25rem]/8 cursor-pointer transition-colors"
      :class="{
        'bg-primary text-primary-foreground': preferencesStore.fontSize === 'normal',
      }"
    >
      <span class="p-1" :aria-label="t('preferences.fontSizes.large')">A</span>
      <input
        type="radio"
        name="font-size"
        value="normal"
        class="sr-only"
        v-model="preferencesStore.fontSize"
      />
    </label>
  </fieldset>
</template>
