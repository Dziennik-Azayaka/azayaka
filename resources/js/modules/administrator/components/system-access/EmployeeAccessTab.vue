<script setup lang="ts">
import EmployeeAccessTable from './EmployeeAccessTable.vue';
import { useGetEmployeeAccesses } from '@/api/hooks/employee/getEmployeeAcceses';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';

const { data: accesses, isFetching, isError, refetch } = useGetEmployeeAccesses();
</script>

<template>
  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError v-else-if="isError" @refresh="refetch" />
  <EmployeeAccessTable v-else-if="accesses" :accesses="accesses" />
</template>
