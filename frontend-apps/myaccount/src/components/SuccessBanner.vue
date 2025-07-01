<script setup lang="ts">
import { LucideCheckCircle } from "lucide-vue-next";

defineProps<{ description: string }>();
</script>

<template>
    <div
        class="border border-green-700 text-green-700 bg-green-700/10 dark:border-green-300 dark:text-green-300 dark:bg-green-300/10 text-sm p-3 rounded-md flex gap-3 items-center"
        role="alert"
    >
        <LucideCheckCircle aria-hidden="true" :size="18" />
        <p>
            {{ description }}
        </p>
    </div>
</template>
