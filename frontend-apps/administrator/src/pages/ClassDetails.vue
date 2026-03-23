<script setup lang="ts">
import { useI18n } from "vue-i18n";

import PanelHeader from "@/components/PanelHeader.vue";

const props = defineProps<{ classId: number }>();

const { t } = useI18n();
</script>

<template>
    <div>
        <PanelHeader
            :breadcrumb-path="[
                { href: { name: 'classes.list' }, title: t('classes') },
                { href: { name: 'classes.details', params: { id: props.classId } }, title: t('classDetails') },
            ]"
        />
        <h1 class="text-2xl font-semibold">{{ t("class") }} with id = {{ props.classId }}</h1>
    </div>
</template>
