<script setup lang="ts">
import type { ActivityLogEntry } from '@/api/types/activity-log-entry';
import { useI18n } from 'vue-i18n';

defineProps<{ activity: ActivityLogEntry }>();

const { t } = useI18n();
</script>

<template>
  <template v-if="activity.device.os || activity.device.name || activity.device.software">
    <template v-if="activity.device.name"> {{ activity.device.name }}, </template>
    {{ activity.device.os }}
    <template v-if="activity.device.software"> ({{ activity.device.software }}), </template>
  </template>
  <template v-else>{{ t('myAccount.data.unknownDevice') }}</template>
  {{ t('myAccount.data.ipAddress') }}:
  {{ activity.device.ipAddress }}
</template>
