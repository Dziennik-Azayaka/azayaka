<script setup lang="ts">
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import ClassesTab from '../components/classes/ClassesTab.vue';
import type { GetClassFilter } from '@/api/dtos/class';

const { t } = useI18n();
const tab = ref<GetClassFilter>('current');
</script>

<template>
  <PanelPageHeader
    :title="t('administrator.classes.title')"
    :description="t('administrator.classes.description')"
  />
  <Tabs v-model="tab" class="flex-1">
    <div class="flex justify-between gap-2 not-md:flex-col not-md:items-stretch">
      <TabsList>
        <TabsTrigger value="archive">{{ t('administrator.classes.tabs.archive') }}</TabsTrigger>
        <TabsTrigger value="current">{{ t('administrator.classes.tabs.current') }}</TabsTrigger>
        <TabsTrigger value="future">{{ t('administrator.classes.tabs.future') }}</TabsTrigger>
      </TabsList>
    </div>
    <TabsContent :value="tab" :key="tab" class="flex flex-col">
      <ClassesTab :tab="tab" />
    </TabsContent>
  </Tabs>
</template>
