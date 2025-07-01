<script lang="ts" setup>
import { useFormField } from "./useFormField";
import type { HTMLAttributes } from "vue";

import { cn } from "#ui/lib/utils";

const props = defineProps<{
    class?: HTMLAttributes["class"];
}>();

const { formDescriptionId } = useFormField();
</script>

<template>
    <p :id="formDescriptionId" data-slot="form-description" :class="cn('text-muted-foreground text-sm', props.class)">
        <slot />
    </p>
</template>
