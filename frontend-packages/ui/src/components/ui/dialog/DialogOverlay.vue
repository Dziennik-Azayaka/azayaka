<script setup lang="ts">
import { reactiveOmit } from "@vueuse/core";
import { DialogOverlay, type DialogOverlayProps } from "reka-ui";
import type { HTMLAttributes } from "vue";

import { cn } from "#ui/lib/utils";

const props = defineProps<DialogOverlayProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
    <DialogOverlay
        data-slot="dialog-overlay"
        v-bind="delegatedProps"
        :class="
            cn(
                'data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 fixed inset-0 z-50 bg-black/80',
                props.class,
            )
        "
    >
        <slot />
    </DialogOverlay>
</template>
