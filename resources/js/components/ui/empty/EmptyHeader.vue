<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div
    data-slot="empty-header"
    :class="cn('flex max-w-md flex-col items-center gap-2 text-center', props.class)"
  >
    <slot />
  </div>
</template>
