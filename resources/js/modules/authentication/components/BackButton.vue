<script setup lang="ts">
import { backOrPush } from '@/lib/utils';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ routeName: string }>();

const back = () => backOrPush(props.routeName);

const { t } = useI18n();
</script>

<template>
  <button
    type="button"
    @click="back"
    class="hover:underline inline font-medium text-sm cursor-pointer text-primary"
  >
    {{ t('common.actions.back') }}
  </button>
</template>
