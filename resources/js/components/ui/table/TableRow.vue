<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <tr
    data-slot="table-row"
    :class="
      cn(
        'hover:bg-accent/50 data-[state=selected]:bg-accent border-b transition-colors',
        props.class,
      )
    "
  >
    <slot />
  </tr>
</template>
