<script setup lang="ts">
import { ApiError } from '@/api/error';
import { LucideAlertCircle } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ error: Error }>();

const translationId = computed(() =>
  props.error instanceof ApiError ? props.error.getTranslationId() : 'apiErrors.unexpectedError',
);

const { t } = useI18n();
</script>

<template>
  <div
    class="border border-destructive text-destructive text-sm p-3 rounded-md bg-destructive/10 flex gap-3 items-center"
    role="alert"
  >
    <LucideAlertCircle aria-hidden="true" :size="18" />
    <p>
      {{ t(translationId) }}
    </p>
  </div>
</template>
