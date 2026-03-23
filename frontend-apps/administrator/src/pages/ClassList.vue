<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from "#ui/components/ui/tabs";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import SchoolStructureService from "@/api/services/school-structure";
import PanelHeader from "@/components/PanelHeader.vue";
import ClassAddDialog from "@/components/classes/ClassAddDialog.vue";
import ClassesTab from "@/components/classes/ClassesTab.vue";

const { t } = useI18n();

function createUnitsPromise() {
    return (async () => {
        const units = await SchoolStructureService.getSchoolUnits();
        isPromiseResolved.value = true;
        return units;
    })();
}

const isPromiseResolved = ref(false);
const unitsPromise = ref(createUnitsPromise());
const tab = ref("current");

function refreshUnitsPromise() {
    isPromiseResolved.value = false;
    unitsPromise.value = createUnitsPromise();
}
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'classes' }, title: t('classes') }]" />
        <h1 class="text-2xl font-semibold">{{ t("classes") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("classesDescription") }}</p>
        <Tabs v-model="tab">
            <div class="flex justify-between gap-2 not-md:flex-col not-md:items-stretch">
                <TabsList>
                    <TabsTrigger value="future">{{ t("futureClasses") }}</TabsTrigger>
                    <TabsTrigger value="current">{{ t("currentClasses") }}</TabsTrigger>
                    <TabsTrigger value="archive">{{ t("archive") }}</TabsTrigger>
                </TabsList>
                <ClassAddDialog v-if="isPromiseResolved" :units-promise="unitsPromise" />
            </div>
            <TabsContent :value="tab" v-for="tab in ['future', 'current', 'archive'] as const" :key="tab">
                <ClassesTab :tab="tab" :units-promise="unitsPromise" @units-load-failed="refreshUnitsPromise()" />
            </TabsContent>
        </Tabs>
    </div>
</template>
