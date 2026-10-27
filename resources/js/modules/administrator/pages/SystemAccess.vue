<script setup lang="ts">
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AccessTab from '@/modules/administrator/components/system-access/AccessTab.vue';
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
      <TabsTrigger value="students">
        {{ t('administrator.systemAccess.tabs.students') }}
      </TabsTrigger>
      <TabsTrigger value="guardians">
        {{ t('administrator.systemAccess.tabs.guardians') }}
      </TabsTrigger>
    </TabsList>
    <TabsContent value="employees" class="flex-1 flex-col flex">
      <AccessTab type="employee" />
    </TabsContent>
    <TabsContent value="students" class="flex-1 flex-col flex">
      <AccessTab type="student" />
    </TabsContent>
    <TabsContent value="guardians" class="flex-1 flex-col flex">
      <AccessTab type="guardian" />
    </TabsContent>
  </Tabs>
</template>
