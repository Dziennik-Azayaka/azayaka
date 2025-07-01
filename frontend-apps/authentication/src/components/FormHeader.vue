<script setup lang="ts">
defineProps<{ title: string; subtitle?: string }>();
</script>

<template>
    <h1 class="text-2xl font-bold leading-9 tracking-tight">
        {{ title }}
    </h1>
    <p class="text-sm leading-6 text-foreground/70" v-if="subtitle">
        {{ subtitle }}
    </p>
</template>
