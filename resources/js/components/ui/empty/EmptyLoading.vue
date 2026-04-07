<script setup lang="ts">
import Empty from './Empty.vue';
import EmptyContent from './EmptyContent.vue';
import { LucideLoaderCircle } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
  <Empty>
    <EmptyContent>
      <LucideLoaderCircle class="animate-spin" :aria-label="t('common.pleaseWait')" />
    </EmptyContent>
  </Empty>
</template>
