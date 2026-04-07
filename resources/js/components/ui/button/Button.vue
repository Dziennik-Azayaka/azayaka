<script setup lang="ts">
import { type ButtonVariants, buttonVariants } from '.';
import { cn } from '@/lib/utils.ts';
import { LoaderCircle } from 'lucide-vue-next';
import { Primitive, type PrimitiveProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props extends PrimitiveProps {
  variant?: ButtonVariants['variant'];
  size?: ButtonVariants['size'];
  class?: HTMLAttributes['class'];
  loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  as: 'button',
  loading: false,
});

const { t } = useI18n();
</script>

<template>
  <Primitive
    data-slot="button"
    :as="as"
    :as-child="asChild"
    :disabled="loading || undefined"
    :class="cn(buttonVariants({ variant, size }), 'relative', props.class)"
  >
    <slot />

    <div
      v-if="loading"
      class="absolute inset-0 flex items-center justify-center bg-inherit rounded-lg"
    >
      <LoaderCircle class="animate-spin size-5" :aria-label="t('common.pleaseWait')" />
    </div>
  </Primitive>
</template>
