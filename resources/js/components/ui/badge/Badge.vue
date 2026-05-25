<script setup lang="ts">
import { cn } from '@/lib/utils.ts';
import { type HTMLAttributes, computed } from 'vue';

interface Props {
  variant?: 'default' | 'secondary';
  class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
});

const variantClasses = computed(() =>
  cn(
    'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors',
    props.variant === 'default' && 'border-transparent bg-primary text-primary-foreground',
    props.variant === 'secondary' && 'border-transparent bg-secondary text-secondary-foreground',
    props.class,
  ),
);
</script>

<template>
  <span :class="variantClasses">
    <slot />
  </span>
</template>
