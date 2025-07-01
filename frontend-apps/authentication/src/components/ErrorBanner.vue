<script setup lang="ts">
import { LucideAlertCircle } from "lucide-vue-next";

defineProps<{ description: string }>();
</script>

<template>
    <div
        class="border border-destructive text-destructive text-sm p-3 rounded-md bg-destructive/10 flex gap-3 items-center"
        role="alert"
    >
        <LucideAlertCircle aria-hidden="true" :size="18" />
        <p>{{ description }}</p>
    </div>
</template>
