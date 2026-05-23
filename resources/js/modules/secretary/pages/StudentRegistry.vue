<script setup lang="ts">
import { computed } from 'vue';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { useSecretaryStore } from '@/stores/secretary';
import { useI18n } from 'vue-i18n';
import NotCreatedInfo from '../components/student-registry/NotCreatedInfo.vue';
import { useGetStudentRegistryId } from '@/api/hooks/student-registry/getStudentRegistryId';
import { EmptyLoading } from '@/components/ui/empty';
import EmptyLoadingError from '@/components/ui/empty/EmptyLoadingError.vue';
import StudentRegistryContainer from '../components/student-registry/StudentRegistryContainer.vue';

const { t } = useI18n();
const secretaryStore = useSecretaryStore();

const unitId = computed(() => secretaryStore.selectedUnit?.id);
const { data: registryId, refetch, isFetching, isError } = useGetStudentRegistryId(unitId);
</script>

<template>
  <PanelPageHeader
    :title="
      secretaryStore.selectedUnit?.studentCategory === 'adultsOnly'
        ? t('secretary.listenerRegistry.title')
        : t('secretary.studentRegistry.title')
    "
  />
  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError v-else-if="isError" @refresh="refetch" />
  <StudentRegistryContainer :registry-id="registryId" v-else-if="registryId !== null && registryId !== undefined" />
  <NotCreatedInfo v-else />
</template>
