<script setup lang="ts">
import { useGetEmployees } from '@/api/hooks/employee/getEmployees';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import EmployeeAdd from '@/modules/administrator/components/employees/EmployeeAdd.vue';
import EmployeeTable from '@/modules/administrator/components/employees/EmployeeTable.vue';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const tab = ref('current');
const { data: employees, isFetching, isError, refetch } = useGetEmployees();
</script>

<template>
  <PanelPageHeader
    :title="t('administrator.employees.title')"
    :description="t('administrator.employees.description')"
  />

  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError v-else-if="isError" @refresh="refetch" />

  <template v-else-if="employees">
    <Tabs v-model="tab">
      <div class="flex justify-between gap-2 not-md:flex-col not-md:items-stretch">
        <TabsList>
          <TabsTrigger value="current">{{ t('administrator.employees.tabs.current') }}</TabsTrigger>
          <TabsTrigger value="archive">{{ t('administrator.employees.tabs.archive') }}</TabsTrigger>
        </TabsList>
        <EmployeeAdd />
      </div>
      <TabsContent value="current">
        <EmployeeTable :employees="employees.filter((employee) => employee.active)" />
      </TabsContent>
      <TabsContent value="archive">
        <EmployeeTable :employees="employees.filter((employee) => !employee.active)" />
      </TabsContent>
    </Tabs>
  </template>
</template>
