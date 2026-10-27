<script setup lang="ts">
import AccessTable from './AccessTable.vue';
import { useGetAccesses } from '@/api/hooks/access/getAccesses';
import type { AccessType } from '@/api/types/access';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';

const props = defineProps<{ type: AccessType }>();

const { data: accesses, isLoading, isError, refetch } = useGetAccesses(props.type);
</script>

<template>
  <EmptyLoading v-if="isLoading" />
  <EmptyLoadingError v-else-if="isError" @refresh="refetch" />
  <AccessTable v-else-if="accesses" :accesses="accesses" :type="type" />
</template>
