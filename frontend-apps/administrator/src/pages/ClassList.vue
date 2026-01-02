<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from "#ui/components/ui/tabs";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import SchoolStructureService from "@/api/services/school-structure";
import PanelHeader from "@/components/PanelHeader.vue";
import ClassesTab from "@/components/classes/ClassesTab.vue";

const { t } = useI18n();

const unitsPromise = ref(SchoolStructureService.getUnits());
const tab = ref("current");

function refreshUnitsPromise() {
    unitsPromise.value = SchoolStructureService.getUnits();
}
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'classes' }, title: t('classes') }]" />
        <h1 class="text-2xl font-semibold">{{ t("classes") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("classesDescription") }}</p>
        <Tabs v-model="tab">
            <TabsList>
                <TabsTrigger value="future">{{ t("futureClasses") }}</TabsTrigger>
                <TabsTrigger value="current">{{ t("currentClasses") }}</TabsTrigger>
                <TabsTrigger value="archive">{{ t("archive") }}</TabsTrigger>
            </TabsList>
            <TabsContent :value="tab" v-for="tab in ['future', 'current', 'archive'] as const" :key="tab">
                <ClassesTab :tab="tab" :units-promise="unitsPromise" @units-load-failed="refreshUnitsPromise()" />
            </TabsContent>
        </Tabs>
    </div>
</template>
