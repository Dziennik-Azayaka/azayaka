<script setup lang="ts">
import type { HTMLAttributes } from "vue";

import { cn } from "#ui/lib/utils";

const props = defineProps<{
    class?: HTMLAttributes["class"];
}>();
</script>

<template>
    <tr
        data-slot="table-row"
        :class="cn('hover:bg-accent/50 data-[state=selected]:bg-accent border-b transition-colors', props.class)"
    >
        <slot />
    </tr>
</template>
