<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from "#ui/components/ui/tabs";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import PanelHeader from "@/components/PanelHeader.vue";
import SubjectsTable from "@/components/subjects/SubjectsTable.vue";

const { t } = useI18n();

const tab = ref("active");
const subjects = [
    {
        id: 1,
        name: "Matematyka",
        shortcut: "mat.",
        active: true,
    },
    {
        id: 2,
        name: "Język angielski",
        shortcut: "j. ang.",
        active: false,
    },
    {
        id: 3,
        name: "Tworzenie zaawansowanych aplikacji webowych",
        shortcut: "tw. zaaw. apl. web.",
        active: true,
    },
];
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'subjects' }, title: t('subjects') }]" />
        <h1 class="text-2xl font-semibold">{{ t("subjects") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("subjectsDescription") }}</p>
        <Tabs v-model="tab">
            <TabsList>
                <TabsTrigger value="active">{{ t("currentSubjects") }}</TabsTrigger>
                <TabsTrigger value="not-active">{{ t("archive") }}</TabsTrigger>
            </TabsList>
            <TabsContent value="active">
                <SubjectsTable :subjects="subjects" />
            </TabsContent>
            <TabsContent value="not-active">
                <SubjectsTable :subjects="subjects" />
            </TabsContent>
        </Tabs>
    </div>
</template>
