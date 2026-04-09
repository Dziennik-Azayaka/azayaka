<script setup lang="ts" generic="T">
import { Checkbox } from '@/components/ui/checkbox';
import type { Row } from '@tanstack/vue-table';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ row: Row<T> }>();

const { t } = useI18n();

function onClickOutside(event: Event) {
  event.stopPropagation();
  props.row.toggleSelected();
}
</script>

<template>
  <div class="px-4 py-3 h-5 box-content" @click="onClickOutside">
    <Checkbox
      :model-value="row.getIsSelected()"
      @update:model-value="(value) => row.toggleSelected(!!value)"
      :aria-label="t('common.actions.selectRow')"
      @click="(event: Event) => event.stopPropagation()"
    />
  </div>
</template>
