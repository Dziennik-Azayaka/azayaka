<script lang="ts" setup>
import { useFormField } from "./useFormField";
import { Slot } from "reka-ui";

const { error, formItemId, formDescriptionId, formMessageId } = useFormField();
</script>

<template>
    <Slot
        :id="formItemId"
        data-slot="form-control"
        :aria-describedby="!error ? `${formDescriptionId}` : `${formDescriptionId} ${formMessageId}`"
        :aria-invalid="!!error"
    >
        <slot />
    </Slot>
</template>
