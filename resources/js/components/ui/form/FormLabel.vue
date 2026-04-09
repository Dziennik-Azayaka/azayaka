<script lang="ts" setup>
import { useFormField } from './useFormField';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import type { LabelProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<LabelProps & { class?: HTMLAttributes['class']; required?: boolean }>();

const { error, formItemId } = useFormField();
</script>

<template>
  <Label
    data-slot="form-label"
    :data-error="!!error"
    :class="cn('data-[error=true]:text-destructive-foreground', props.class)"
    :for="formItemId"
  >
    <slot />

    <span class="text-destructive" v-if="required">*</span>
  </Label>
</template>
