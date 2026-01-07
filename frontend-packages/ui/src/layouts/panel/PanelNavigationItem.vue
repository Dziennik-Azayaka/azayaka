<script setup lang="ts">
import type { Component } from "vue";
import type { RouteLocationAsPathGeneric, RouteLocationAsRelativeGeneric } from "vue-router";

defineProps<{
    title: string;
    icon: Component;
    link: string | RouteLocationAsRelativeGeneric | RouteLocationAsPathGeneric;
}>();
</script>

<template>
    <li>
        <RouterLink
            :to="link"
            class="flex gap-3 px-3.5 md:px-2.5 py-2.5 md:py-1.5 rounded-md items-center hover:bg-sidebar-accent transition-colors text-foreground/90"
            active-class="!bg-primary !text-primary-foreground"
        >
            <component :is="icon" aria-hidden="true" class="w-5" />
            <span class="font-medium text-sm">{{ title }}</span>
        </RouterLink>
    </li>
</template>
