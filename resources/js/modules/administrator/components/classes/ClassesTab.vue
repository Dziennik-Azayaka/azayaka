<script setup lang="ts">
import ClassTable from './ClassTable.vue';
import type { GetClassFilter } from '@/api/dtos/class';
import { useGetClasses } from '@/api/hooks/classes/getClasses';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';
import { computed } from 'vue';

const props = defineProps<{ tab: GetClassFilter }>();

const { data: classes, isFetching, isError, refetch } = useGetClasses(computed(() => props.tab));
</script>

<template>
  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError @refresh="refetch" v-else-if="isError" />
  <ClassTable v-else-if="classes" :classes="classes" :show-current-level="tab === 'current'" />
</template>
