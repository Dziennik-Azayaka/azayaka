<script setup lang="ts">
import { reactiveOmit } from "@vueuse/core";
import { Circle } from "lucide-vue-next";
import {
    DropdownMenuItemIndicator,
    DropdownMenuRadioItem,
    type DropdownMenuRadioItemEmits,
    type DropdownMenuRadioItemProps,
    useForwardPropsEmits,
} from "reka-ui";
import type { HTMLAttributes } from "vue";

import { cn } from "#ui/lib/utils";

const props = defineProps<DropdownMenuRadioItemProps & { class?: HTMLAttributes["class"] }>();

const emits = defineEmits<DropdownMenuRadioItemEmits>();

const delegatedProps = reactiveOmit(props, "class");

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <DropdownMenuRadioItem
        data-slot="dropdown-menu-radio-item"
        v-bind="forwarded"
        :class="
            cn(
                `focus:bg-accent focus:text-accent-foreground relative flex cursor-pointer items-center gap-2 rounded-sm py-1.5 pr-2 pl-8 text-sm outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4`,
                props.class,
            )
        "
    >
        <span class="pointer-events-none absolute left-2 flex size-3.5 items-center justify-center">
            <DropdownMenuItemIndicator>
                <Circle class="size-2 fill-current" />
            </DropdownMenuItemIndicator>
        </span>
        <slot />
    </DropdownMenuRadioItem>
</template>
