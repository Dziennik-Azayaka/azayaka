<script lang="ts" setup>
import { useFormField } from "./useFormField";
import type { LabelProps } from "reka-ui";
import type { HTMLAttributes } from "vue";

import { Label } from "#ui/components/ui/label";
import { cn } from "#ui/lib/utils";

const props = defineProps<LabelProps & { class?: HTMLAttributes["class"] }>();

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
    </Label>
</template>
