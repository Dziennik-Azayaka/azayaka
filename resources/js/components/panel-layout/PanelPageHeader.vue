<script setup lang="ts">
defineProps<{ title: string; description?: string }>();
</script>

<template>
  <div class="mb-4">
    <h1 class="text-2xl font-semibold mt-4">{{ title }}</h1>
    <p class="text-muted-foreground text-sm" v-if="description">{{ description }}</p>
  </div>
</template>
