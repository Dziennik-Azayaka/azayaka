<script setup lang="ts">
import { appName, appVersion } from '@/env.ts';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
  <div class="min-h-dvh p-5 md:p-12 flex items-center justify-center">
    <div class="space-y-8">
      <div class="relative">
        <h1
          class="text-3xl lg:text-5xl font-bold absolute top-[50%] left-0 right-0 text-center translate-y-[-50%]"
        >
          {{ t('notFound.title') }}
        </h1>
        <p
          aria-hidden="true"
          class="text-[10rem] lg:text-[18rem] font-extrabold text-muted -z-10 select-none leading-72"
        >
          404
        </p>
      </div>
      <footer class="text-muted-foreground justify-between flex text-sm">
        <span>{{ appName }} {{ appVersion }}</span>
        <span>&copy; 2025 &ndash; 2026</span>
      </footer>
    </div>
  </div>
</template>
