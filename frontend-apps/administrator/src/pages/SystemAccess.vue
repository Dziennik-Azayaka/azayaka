<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from "#ui/components/ui/tabs";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import PanelHeader from "@/components/PanelHeader.vue";
import EmployeeAccessesTab from "@/components/system-access/EmployeeAccessesTab.vue";

const { t } = useI18n();
const tab = ref("employees");
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'systemAccess' }, title: t('systemAccess') }]" />
        <h1 class="text-2xl font-semibold">{{ t("systemAccess") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("systemAccessDescription") }}</p>
        <Tabs v-model="tab">
            <TabsList>
                <TabsTrigger value="employees">{{ t("employees") }}</TabsTrigger>
            </TabsList>
            <TabsContent value="employees">
                <EmployeeAccessesTab />
            </TabsContent>
        </Tabs>
    </div>
</template>
