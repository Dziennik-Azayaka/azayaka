<script setup lang="ts">
import AccountActivityTableDevice from './AccountActivityTableDevice.vue';
import type { ActivityLogEntry } from '@/api/types/activity-log-entry';
import {
  Pagination,
  PaginationContent,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination';
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { createColumnHelper, getCoreRowModel, useVueTable } from '@tanstack/vue-table';
import { FlexRender } from '@tanstack/vue-table';
import { h } from 'vue';
import { useI18n } from 'vue-i18n';

const page_ = defineModel<number>({ default: 1 });
const props = defineProps<{
  data: ActivityLogEntry[];
  paginationInfo: { total: number; page: number; perPage: number };
}>();

const { t, d } = useI18n();

const columnHelper = createColumnHelper<ActivityLogEntry>();
const columns = [
  columnHelper.accessor('type', {
    header: () => t('myAccount.data.activity'),
    cell: ({ getValue }) => t(activityNameTranslationId(getValue())),
  }),
  columnHelper.accessor('date', {
    header: () => t('common.data.date'),
    cell: ({ getValue }) => d(getValue(), 'long'),
  }),
  columnHelper.accessor('device', {
    header: () => t('myAccount.data.device'),
    cell: ({ row }) => h(AccountActivityTableDevice, { activity: row.original }),
  }),
];

const table = useVueTable({
  get data() {
    return props.data;
  },
  getRowId: (entry) => entry.date.toISOString(),
  columns,
  getCoreRowModel: getCoreRowModel(),
});

function activityNameTranslationId(type: ActivityLogEntry['type']) {
  switch (type) {
    case 'failed_login_attempt':
      return 'myAccount.activityHistory.statuses.failedLoginAttempt';
    case 'logged_out_by_another_device':
      return 'myAccount.activityHistory.statuses.loggedOutByAnotherDevice';
    case 'logout':
      return 'myAccount.activityHistory.statuses.loggedOut';
    case 'credentials_changed':
      return 'myAccount.activityHistory.statuses.credentialsChanged';
    case 'successful_login_attempt':
      return 'myAccount.activityHistory.statuses.loggedIn';
  }
}
</script>

<template>
  <TableContainer>
    <Table>
      <TableHeader>
        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
          <TableHead v-for="header in headerGroup.headers" :key="header.id">
            <FlexRender
              v-if="!header.isPlaceholder"
              :props="header.getContext()"
              :render="header.column.columnDef.header"
            />
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow
          v-for="row in table.getRowModel().rows"
          :key="row.id"
          :current-data="row.original"
        >
          <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
            <FlexRender :props="cell.getContext()" :render="cell.column.columnDef.cell" />
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </TableContainer>

  <Pagination
    v-model:page="page_"
    v-slot="{ page }"
    :items-per-page="paginationInfo.perPage"
    :total="paginationInfo.total"
    :default-page="paginationInfo.page"
    class="mt-4"
  >
    <PaginationContent v-slot="{ items }">
      <PaginationPrevious />
      <template v-for="(item, index) in items" :key="index">
        <PaginationItem
          v-if="item.type === 'page'"
          :value="item.value"
          :is-active="item.value === page"
        >
          {{ item.value }}
        </PaginationItem>
      </template>
      <PaginationNext />
    </PaginationContent>
  </Pagination>
</template>
