<script setup lang="ts">
import SchoolComplexCard from '@/modules/administrator/components/school-structure/SchoolComplexCard.vue';
import SchoolComplexCreate from '@/modules/administrator/components/school-structure/SchoolComplexCreate.vue';
import SchoolUnitCard from '@/modules/administrator/components/school-structure/SchoolUnitCard.vue';
import { useGetSchoolStructure } from '@/api/hooks/school-structure/getSchoolStructure';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';
import { LucideInfo } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const { data: structure, isFetching, isError, refetch } = useGetSchoolStructure();
</script>

<template>
  <PanelPageHeader :title="t('administrator.schoolStructure.title')" />

  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError v-else-if="isError" @refresh="refetch" />

  <template v-else-if="structure">
    <div class="flex justify-end mb-4" v-if="structure.mode === 'single'">
      <SchoolComplexCreate />
    </div>
    <ul class="space-y-6">
      <SchoolUnitCard v-if="structure.mode === 'single'" :unit="structure.unit" />
      <SchoolComplexCard :complex="structure.complex" v-else />
    </ul>
    <p
      class="border rounded-md bg-primary/20 text-primary border-primary text-sm p-3 mt-5 flex gap-2 items-center"
      v-if="structure.mode === 'single'"
    >
      <LucideInfo class="size-4" />
      {{ t('administrator.schoolStructure.schoolComplexInfo') }}
    </p>
  </template>
</template>
