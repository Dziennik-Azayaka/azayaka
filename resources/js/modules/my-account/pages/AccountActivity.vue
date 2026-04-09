<script setup lang="ts">
import { useGetAccountActivity } from '@/api/hooks/user/getAccountActivity';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';
import AccountActivityTable from '@/modules/my-account/components/AccountActivityTable.vue';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const page = ref(1);
const { data: resource, isFetching, isError, refetch } = useGetAccountActivity(page);

const paginationInfo = ref({
  page: 1,
  perPage: 50,
  total: 0,
});

watch(resource, (value) => {
  if (!value) return;

  paginationInfo.value = { page: value.currentPage, perPage: value.perPage, total: value.total };
});
</script>

<template>
  <PanelPageHeader :title="t('myAccount.activityHistory.title')" />

  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError v-else-if="isError" @refresh="refetch" />
  <AccountActivityTable
    v-else-if="resource"
    :data="resource.data"
    :pagination-info="paginationInfo"
    v-model="page"
  />
</template>
