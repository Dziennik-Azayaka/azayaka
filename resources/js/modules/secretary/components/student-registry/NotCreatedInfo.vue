<script setup lang="ts">
import { useCreateStudentRegistry } from '@/api/hooks/student-registry/createStudentRegistry';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import { Empty, EmptyContent, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { useSecretaryStore } from '@/stores/secretary';
import { LucideBookUser, LucidePlus } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const secretaryStore = useSecretaryStore();

const { mutate, isPending, error } = useCreateStudentRegistry();
const create = () => mutate(secretaryStore.selectedUnit!.id);
</script>

<template>
  <Empty class="border border-dashed border-foreground/30">
    <EmptyHeader>
      <EmptyMedia variant="icon">
        <LucideBookUser />
      </EmptyMedia>
      <EmptyTitle>{{ t('secretary.studentRegistry.notCreatedInfo.title') }}</EmptyTitle>
    </EmptyHeader>
    <EmptyContent>
      <Button :loading="isPending" @click="create">
        <LucidePlus />
        {{ t('secretary.studentRegistry.notCreatedInfo.button') }}
      </Button>
      <ErrorBanner v-if="error" :error="error" />
    </EmptyContent>
  </Empty>
</template>
