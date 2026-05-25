<script setup lang="ts">
import { Info } from 'lucide-vue-next';

defineProps<{ description: string }>();
</script>

<template>
  <div
    class="border border-blue-700 text-blue-700 bg-blue-700/10 dark:border-blue-300 dark:text-blue-300 dark:bg-blue-300/10 text-sm p-3 rounded-md flex gap-3 items-center"
    role="alert"
  >
    <Info aria-hidden="true" :size="18" />
    <p>
      {{ description }}
    </p>
  </div>
</template>
