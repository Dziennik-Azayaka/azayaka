<script setup lang="ts">
import PanelHead from '@/components/panel-layout/PanelHead.vue';
import { A11ySkipToMain } from '@/components/ui/a11y';
</script>

<template>
  <A11ySkipToMain />
  <div class="w-screen h-dvh flex gap-3 xl:p-3 items-stretch xl:bg-sidebar">
    <slot name="navigation" />
    <main
      class="flex-1 flex flex-col rounded-md xl:border bg-background overflow-y-auto p-4 md:p-8 md:pt-4"
    >
      <PanelHead />
      <RouterView v-slot="{ Component, route }">
        <transition
          mode="out-in"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
          enter-active-class="transition-opacity duration-200"
          leave-active-class="transition-opacity duration-200"
        >
          <div class="flex-1 flex flex-col" id="main-content" :key="route.fullPath">
            <component :is="Component" />
          </div>
        </transition>
      </RouterView>
    </main>
  </div>
</template>
