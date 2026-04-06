<script setup lang="ts">
import { type ButtonVariants, buttonVariants } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { reactiveOmit } from '@vueuse/core';
import { ChevronRightIcon } from 'lucide-vue-next';
import type { PaginationNextProps } from 'reka-ui';
import { PaginationNext, useForwardProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { useI18n } from 'vue-i18n';

const props = withDefaults(
  defineProps<
    PaginationNextProps & {
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
const { t } = useI18n();
</script>

<template>
  <PaginationNext
    data-slot="pagination-next"
    :class="cn(buttonVariants({ variant: 'ghost', size }), 'gap-1 px-2.5 sm:pr-2.5', props.class)"
    v-bind="forwarded"
  >
    <slot>
      <span class="hidden sm:block">{{ t('common.pagination.nextPage') }}</span>
      <ChevronRightIcon />
    </slot>
  </PaginationNext>
</template>
