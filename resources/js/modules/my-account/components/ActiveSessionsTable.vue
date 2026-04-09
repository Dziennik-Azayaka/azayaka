<script setup lang="ts">
import ActiveSessionsTableDevice from './ActiveSessionsTableDevice.vue';
import ActiveSessionsTableRemove from './ActiveSessionsTableRemove.vue';
import type { SessionListEntry } from '@/api/types/session-list';
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  createColumnHelper,
  FlexRender,
  getCoreRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table';
import { h } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ sessions: SessionListEntry[]; currentSessionId: string }>();

const { t, d } = useI18n();

const columnHelper = createColumnHelper<SessionListEntry>();
const columns = [
  columnHelper.accessor('device', {
    header: () => t('myAccount.data.device'),
    cell: ({ row }) =>
      h(ActiveSessionsTableDevice, {
        session: row.original,
        isCurrent: row.original.id === props.currentSessionId,
      }),
  }),
  columnHelper.accessor('lastActivityDate', {
    header: () => t('myAccount.data.lastActivity'),
    cell: ({ cell }) => d(cell.getValue(), 'long'),
  }),
  columnHelper.display({
    id: 'actions',
    header: () => t('common.actions_'),
    cell: ({ row }) =>
      row.original.id !== props.currentSessionId
        ? h(ActiveSessionsTableRemove, { sessionId: row.original.id })
        : '-',
  }),
];

const table = useVueTable({
  get data() {
    return props.sessions;
  },
  getRowId: (session) => session.id,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  state: {
    sorting: [
      {
        id: 'lastActivityDate',
        desc: false,
      },
    ],
  },
});
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
</template>
