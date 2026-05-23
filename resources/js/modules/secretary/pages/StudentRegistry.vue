<script setup lang="ts">
import { computed } from 'vue';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { useSecretaryStore } from '@/stores/secretary';
import { useI18n } from 'vue-i18n';
import NotCreatedInfo from '../components/student-registry/NotCreatedInfo.vue';
import { useGetStudentRegistryId } from '@/api/hooks/student-registry/getStudentRegistryId';

const { t } = useI18n();
const secretaryStore = useSecretaryStore();

const unitId = computed(() => secretaryStore.selectedUnit?.id);
const { data: registryId } = useGetStudentRegistryId(unitId);
</script>

<template>
  <PanelPageHeader
    :title="
      secretaryStore.selectedUnit?.studentCategory === 'adultsOnly'
        ? t('secretary.listenerRegistry.title')
        : t('secretary.studentRegistry.title')
    "
  />
  <NotCreatedInfo v-if="registryId === null" />
</template>
