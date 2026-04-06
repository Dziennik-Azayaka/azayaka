<script setup lang="ts">
import ActiveSessionsTable from '@/modules/my-account/components/ActiveSessionsTable.vue';
import { useGetActiveSessions } from '@/api/hooks/session/getActiveSessions';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const { data, isFetching, isError, refetch } = useGetActiveSessions();
</script>

<template>
  <PanelPageHeader
    :title="t('myAccount.home.title')"
    :description="t('myAccount.home.description')"
  />

  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError v-else-if="isError" @refresh="refetch" />
  <ActiveSessionsTable
    :sessions="data.sessions"
    :current-session-id="data.currentSession"
    v-else-if="data"
  />
</template>
