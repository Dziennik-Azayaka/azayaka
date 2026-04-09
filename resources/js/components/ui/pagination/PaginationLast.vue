<script setup lang="ts">
import { type ButtonVariants, buttonVariants } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { reactiveOmit } from '@vueuse/core';
import { ChevronRightIcon } from 'lucide-vue-next';
import type { PaginationLastProps } from 'reka-ui';
import { PaginationLast, useForwardProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

const props = withDefaults(
  defineProps<
    PaginationLastProps & {
      size?: ButtonVariants['size'];
      class?: HTMLAttributes['class'];
    }
  >(),
  {
    size: 'default',
  },
);

const delegatedProps = reactiveOmit(props, 'class', 'size');
const forwarded = useForwardProps(delegatedProps);
</script>

<template>
  <PaginationLast
    data-slot="pagination-last"
    :class="cn(buttonVariants({ variant: 'ghost', size }), 'gap-1 px-2.5 sm:pr-2.5', props.class)"
    v-bind="forwarded"
  >
    <slot>
      <span class="hidden sm:block">Last</span>
      <ChevronRightIcon />
    </slot>
  </PaginationLast>
</template>
