<script setup lang="ts">
import { Button } from "#ui/components/ui/button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "#ui/components/ui/tabs";
import { useQuery } from "@tanstack/vue-query";
import { LucideAlertCircle, LucideLoaderCircle, LucideRefreshCw } from "lucide-vue-next";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import SubjectService from "@/api/services/subject";
import PanelHeader from "@/components/PanelHeader.vue";
import SubjectAddDialog from "@/components/subjects/SubjectAddDialog.vue";
import SubjectsTable from "@/components/subjects/SubjectsTable.vue";

const { t } = useI18n();
const {
    data: subjects,
    isLoading,
    isError,
    refetch,
} = useQuery({ queryKey: ["subjects"], queryFn: SubjectService.getAllSubjects });

const tab = ref("active");
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'subjects' }, title: t('subjects') }]" />
        <h1 class="text-2xl font-semibold">{{ t("subjects") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("subjectsDescription") }}</p>

        <div class="min-h-96 content-center" v-if="isLoading">
            <LucideLoaderCircle class="animate-spin mx-auto" :aria-label="t('pleaseWait')" />
        </div>

        <div class="min-h-96 flex flex-col items-center justify-center" v-else-if="isError">
            <LucideAlertCircle class="mx-auto size-9" />
            <p class="text-center mt-2 font-medium">{{ t("unknownError") }}</p>
            <Button class="mx-auto mt-3" variant="outline" @click="refetch">
                <LucideRefreshCw />
                {{ t("tryAgain") }}
            </Button>
        </div>

        <div v-else-if="subjects">
            <Tabs v-model="tab">
                <section class="flex justify-between gap-2 not-md:flex-col not-md:items-stretch">
                    <TabsList>
                        <TabsTrigger value="active">{{ t("currentSubjects") }}</TabsTrigger>
                        <TabsTrigger value="not-active">{{ t("archive") }}</TabsTrigger>
                    </TabsList>

                    <SubjectAddDialog @added="refetch" />
                </section>
                <TabsContent value="active">
                    <SubjectsTable :subjects="subjects.filter((subject) => subject.active)" @refresh-needed="refetch" />
                </TabsContent>
                <TabsContent value="not-active">
                    <SubjectsTable
                        :subjects="subjects.filter((subject) => !subject.active)"
                        @refresh-needed="refetch"
                    />
                </TabsContent>
            </Tabs>
        </div>
    </div>
</template>
