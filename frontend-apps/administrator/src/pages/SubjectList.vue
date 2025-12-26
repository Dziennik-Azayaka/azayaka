<script setup lang="ts">
import PanelHeader from "@/components/PanelHeader.vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'subjects' }, title: t('subjects') }]" />
        <h1 class="text-2xl font-semibold">{{ t("subjects") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("subjectsDescription") }}</p>
    </div>
</template>
