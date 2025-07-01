<script setup lang="ts">
import { reactiveOmit } from "@vueuse/core";
import { ChevronLeftIcon } from "lucide-vue-next";
import type { PaginationPrevProps } from "reka-ui";
import { PaginationPrev, useForwardProps } from "reka-ui";
import type { HTMLAttributes } from "vue";

import { type ButtonVariants, buttonVariants } from "#ui/components/ui/button";
import { cn } from "#ui/lib/utils";

const props = withDefaults(
    defineProps<
        PaginationPrevProps & {
            size?: ButtonVariants["size"];
            class?: HTMLAttributes["class"];
            text?: string;
        }
    >(),
    {
        size: "default",
    },
);

const delegatedProps = reactiveOmit(props, "class", "size");
const forwarded = useForwardProps(delegatedProps);
</script>

<template>
    <PaginationPrev
        data-slot="pagination-previous"
        :class="cn(buttonVariants({ variant: 'ghost', size }), 'gap-1 px-2.5 sm:pr-2.5', props.class)"
        v-bind="forwarded"
    >
        <slot>
            <ChevronLeftIcon />
            <span class="hidden sm:block">{{ text ?? "Previous" }}</span>
        </slot>
    </PaginationPrev>
</template>
