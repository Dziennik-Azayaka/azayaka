<script setup lang="ts">
import PanelHeader from "@/components/PanelHeader.vue";
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'helloWorld' }, title: 'helloWorld' }]" />
    </div>
    Hello world
</template>
