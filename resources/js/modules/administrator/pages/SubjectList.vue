<script setup lang="ts">
import SubjectAdd from '../components/subjects/SubjectAdd.vue';
import SubjectTable from '../components/subjects/SubjectTable.vue';
import { useGetSubjects } from '@/api/hooks/subject/getSubjects';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const tab = ref('current');
const { data: subjects, isFetching, isError, refetch } = useGetSubjects();
</script>

<template>
  <PanelPageHeader
    :title="t('administrator.subjects.title')"
    :description="t('administrator.subjects.description')"
  />

  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError v-else-if="isError" @refresh="refetch" />

  <template v-else-if="subjects">
    <Tabs v-model="tab">
      <div class="flex justify-between gap-2 not-md:flex-col not-md:items-stretch">
        <TabsList>
          <TabsTrigger value="current">{{ t('administrator.subjects.tabs.current') }}</TabsTrigger>
          <TabsTrigger value="archive">{{ t('administrator.subjects.tabs.archive') }}</TabsTrigger>
        </TabsList>
        <SubjectAdd />
      </div>
      <TabsContent value="current">
        <SubjectTable :subjects="subjects.filter((subject) => subject.active)" />
      </TabsContent>
      <TabsContent value="archive">
        <SubjectTable :subjects="subjects.filter((subject) => !subject.active)" />
      </TabsContent>
    </Tabs>
  </template>
</template>
