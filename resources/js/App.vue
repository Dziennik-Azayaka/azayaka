<script setup lang="ts">
import { usePreferencesStore } from './stores/preferences';
import { watch } from 'vue';

const preferencesStore = usePreferencesStore();

watch(
  () => preferencesStore.fontSize,
  () => {
    const parentEl = document.querySelector('html');
    if (parentEl) parentEl.style.fontSize = preferencesStore.fontSize === 'large' ? '22px' : '16px';
  },
  { immediate: true },
);
</script>

<template>
  <RouterView />
</template>
