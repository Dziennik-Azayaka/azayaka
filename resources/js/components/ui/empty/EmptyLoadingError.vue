<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Empty,
  EmptyContent,
  EmptyDescription,
  EmptyHeader,
  EmptyMedia,
  EmptyTitle,
} from '@/components/ui/empty';
import { LucideAlertOctagon, LucideRefreshCcw } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const emit = defineEmits(['refresh']);
</script>

<template>
  <Empty>
    <EmptyHeader>
      <EmptyMedia variant="icon" class="bg-destructive text-white">
        <LucideAlertOctagon />
      </EmptyMedia>
      <EmptyTitle>{{ t('apiErrors.unexpectedError') }}</EmptyTitle>
      <EmptyDescription>{{ t('common.loadingDataError') }}</EmptyDescription>
    </EmptyHeader>
    <EmptyContent>
      <Button variant="outline" @click="emit('refresh')">
        <LucideRefreshCcw />
        {{ t('common.actions.tryAgain') }}
      </Button>
    </EmptyContent>
  </Empty>
</template>
