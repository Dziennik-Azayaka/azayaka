<script setup lang="ts">
import ChildrenRegistryNotCreatedInfo from '../components/children-registry/ChildrenRegistryNotCreatedInfo.vue';
import ChildRegistryContainer from '../components/children-registry/ChildRegistryContainer.vue';
import { useGetChildrenRegistryId } from '@/api/hooks/children-registry/getChildrenRegistryId';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { EmptyLoading } from '@/components/ui/empty';
import EmptyLoadingError from '@/components/ui/empty/EmptyLoadingError.vue';
import { useSecretaryStore } from '@/stores/secretary';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const secretaryStore = useSecretaryStore();

const unitId = computed(() => secretaryStore.selectedUnit?.id);
const { data: registryId, refetch, isFetching, isError } = useGetChildrenRegistryId(unitId);
</script>

<template>
  <PanelPageHeader :title="t('secretary.childrenRegistry.title')" />
  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError v-else-if="isError" @refresh="refetch" />
  <ChildRegistryContainer
    :registry-id="registryId!"
    :school-unit-id="unitId!"
    v-else-if="registryId !== null && registryId !== undefined"
  />
  <ChildrenRegistryNotCreatedInfo v-else />
</template>
