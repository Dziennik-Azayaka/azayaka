<script lang="ts" setup>
import type { HTMLAttributes } from "vue";

import { cn } from "#ui/lib/utils";

const props = defineProps<{
    class?: HTMLAttributes["class"];
}>();
</script>

<template>
    <ol
        data-slot="breadcrumb-list"
        :class="
            cn('text-muted-foreground flex flex-wrap items-center gap-1.5 text-sm break-words sm:gap-2.5', props.class)
        "
    >
        <slot />
    </ol>
</template>
