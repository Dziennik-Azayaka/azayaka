<script setup lang="ts">
import LogoDark from '../../../static/logo-dark.svg';
import SkipToMain from '@/components/ui/a11y/SkipToMain.vue';
import { appName, appVersion } from '@/env';
import A11yFontSize from '@/modules/authentication/components/A11yFontSize.vue';
import AuthenticationIllustration from '@/modules/authentication/components/AuthenticationIllustration.vue';
import LanguageSwitcher from '@/modules/authentication/components/LanguageSwitcher.vue';
import ThemeSwitcher from '@/modules/authentication/components/ThemeSwitcher.vue';
import { usePreferencesStore } from '@/stores/preferences';
import { useElementSize } from '@vueuse/core';
import { useTemplateRef } from 'vue';

const preferencesStore = usePreferencesStore();
const view = useTemplateRef('view');
const { height: cardHeight } = useElementSize(view);
</script>

<template>
  <SkipToMain />

  <div class="flex flex-col min-h-dvh max-w-7xl mx-auto py-3 md:py-5 px-6 md:px-12 gap-10">
    <section class="flex gap-5">
      <A11yFontSize />
      <ThemeSwitcher />
      <div class="flex-1" />
      <LanguageSwitcher />
    </section>

    <div class="flex-1 not-md:hidden" />

    <div class="flex items-center justify-center xl:justify-between gap-10">
      <div class="space-y-12 md:max-w-136 w-full">
        <header class="not-sm:flex-col flex gap-3 sm:items-center">
          <div class="size-11 p-2 rounded-md bg-primary" aria-hidden="true">
            <img
              class="size-6.5"
              :src="LogoDark"
              alt="Logo"
              :class="{ 'brightness-0': preferencesStore.colorMode === 'dark' }"
            />
          </div>
          <div>
            <p class="text-lg font-semibold">
              (placeholder) Liceum Ogólnokształcące nr 3 w Gdańsku
            </p>
            <p class="text-sm text-foreground/70">{{ appName }}</p>
          </div>
        </header>
        <main
          id="main-content"
          class="transition-[height]"
          :style="{
            height: cardHeight ? cardHeight + 'px' : undefined,
          }"
        >
          <div ref="view">
            <RouterView v-slot="{ Component }">
              <transition
                mode="out-in"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                enter-active-class="transition-opacity duration-300"
                leave-active-class="transition-opacity duration-300"
              >
                <component :is="Component" />
              </transition>
            </RouterView>
          </div>
        </main>
      </div>
      <AuthenticationIllustration class="not-xl:hidden max-w-136 flex-1" role="presentation" />
    </div>

    <div class="flex-1" />

    <footer class="flex justify-between text-sm text-muted-foreground">
      <p>
        <span class="not-sm:hidden">{{ appName }}</span>
        {{ appVersion }} &copy; 2025 &ndash; 2026
      </p>
    </footer>
  </div>
</template>
