<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { appName, appVersion } from '@/env';
import { usePreferencesStore } from '@/stores/preferences';
import { useMediaQuery } from '@vueuse/core';
import { LucideMenu } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const preferencesStore = usePreferencesStore();

const isMobile = useMediaQuery('(width < 80rem)');
const { t } = useI18n();
</script>

<template>
  <Transition
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      class="z-10 fixed top-0 left-0 right-0 bottom-0 bg-foreground/20 dark:bg-background/60 transition-opacity duration-300"
      aria-hidden="true"
      v-if="preferencesStore.mobileNavOpen && isMobile"
      @click="preferencesStore.mobileNavOpen = !preferencesStore.mobileNavOpen"
    />
  </Transition>
  <Transition
    enter-from-class="-translate-x-full"
    enter-to-class="translate-0"
    leave-from-class="translate-0"
    leave-to-class="-translate-x-full"
  >
    <nav
      class="w-87.5 not-xl:fixed not-xl:bg-background not-xl:p-3 not-xl:h-dvh max-w-[80vw] overflow-y-auto flex flex-col z-20"
      :class="{ 'transition-[translate] duration-300': isMobile }"
      v-if="preferencesStore.mobileNavOpen || !isMobile"
    >
      <Button
        variant="ghost"
        size="icon"
        @click="preferencesStore.mobileNavOpen = false"
        class="xl:hidden"
        :title="t('common.actions.closeMenu')"
      >
        <LucideMenu :aria-label="t('common.actions.closeMenu')" />
      </Button>
      <slot name="header" />
      <ul class="mt-3 space-y-1">
        <slot name="navigation-top" />
      </ul>
      <div class="flex-1" />
      <ul class="space-y-1">
        <slot name="navigation-bottom" />
      </ul>
      <footer class="text-muted-foreground text-sm px-3 py-2">
        <div class="flex justify-between">
          <span>{{ appName }}</span>
          <span>{{ appVersion }}</span>
        </div>
      </footer>
    </nav>
  </Transition>
</template>
