<script lang="ts" setup>
import { useFormField } from "./useFormField";
import { ErrorMessage } from "vee-validate";
import { type HTMLAttributes, toValue } from "vue";

import { cn } from "#ui/lib/utils";

const props = defineProps<{
    class?: HTMLAttributes["class"];
}>();

const { name, formMessageId } = useFormField();
</script>

<template>
    <ErrorMessage
        :id="formMessageId"
        data-slot="form-message"
        as="p"
        :name="toValue(name)"
        :class="cn('text-destructive-foreground text-sm', props.class)"
    />
</template>
