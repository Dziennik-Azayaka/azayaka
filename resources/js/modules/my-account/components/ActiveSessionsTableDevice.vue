<script setup lang="ts">
import type { SessionListEntry } from '@/api/types/session-list';
import { LucideHelpCircle, LucideLaptop, LucideSmartphone } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

defineProps<{ session: SessionListEntry; isCurrent: boolean }>();

const getDeviceTypeByOS = (os: string) => {
  if (os.startsWith('iOS') || os.startsWith('Android')) return 'mobile';
  if (os.startsWith('Linux') || os.startsWith('Windows') || os.startsWith('macOS'))
    return 'desktop';
  return 'other';
};

const { t } = useI18n();
</script>

<template>
  <div class="flex not-sm:flex-col sm:items-center items-start gap-2">
    <LucideLaptop
      class="size-5"
      v-if="session.device.os && getDeviceTypeByOS(session.device.os) === 'desktop'"
      aria-label=""
    />
    <LucideSmartphone
      class="size-5"
      v-if="session.device.os && getDeviceTypeByOS(session.device.os) === 'mobile'"
    />
    <LucideHelpCircle class="size-5" v-else />
    <template v-if="session.device.name"> {{ session.device.name }}, </template>
    {{ session.device.os }}
    <template v-if="session.device.software">({{ session.device.software }}),</template>
    {{ t('myAccount.data.ipAddress') }}: {{ session.device.ipAddress }}
    <span
      class="px-1.5 py-1 rounded-md bg-primary text-primary-foreground text-xs"
      v-if="isCurrent"
    >
      {{ t('myAccount.home.currentSession') }}
    </span>
  </div>
</template>
