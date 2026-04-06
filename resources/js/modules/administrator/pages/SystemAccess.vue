<script setup lang="ts">
import EmployeeAccessTab from '@/modules/administrator/components/system-access/EmployeeAccessTab.vue';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const tab = ref('employees');
</script>

<template>
  <PanelPageHeader
    :title="t('administrator.systemAccess.title')"
    :description="t('administrator.systemAccess.description')"
  />
  <Tabs v-model="tab" class="flex-1">
    <TabsList>
      <TabsTrigger value="employees">
        {{ t('administrator.systemAccess.tabs.employees') }}
      </TabsTrigger>
    </TabsList>
    <TabsContent value="employees" class="flex-1 flex-col flex">
      <EmployeeAccessTab />
    </TabsContent>
  </Tabs>
</template>
