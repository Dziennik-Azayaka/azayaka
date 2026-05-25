<script setup lang="ts">
import type { GradebookGroup, GradebookGroupSubject } from '@/api/types/gradebook-group';
import TableTemplate from '@/components/ui/table/TableTemplate.vue';
import { TableCell, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import {
  createColumnHelper,
  FlexRender,
  getCoreRowModel,
  useVueTable,
} from '@tanstack/vue-table';
import { LucidePlus } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ group: GradebookGroup }>();
const emit = defineEmits<{
  'add-subject': [];
  'edit-subject': [value: GradebookGroupSubject];
}>();
const { t } = useI18n();

const columnHelper = createColumnHelper<GradebookGroupSubject>();
const columns = [
  columnHelper.accessor('subject', {
    header: () => t('gradebook.groups.subject'),
  }),
  columnHelper.accessor('description', {
    header: () => t('gradebook.groups.description'),
  }),
  columnHelper.accessor('teachers', {
    header: () => t('gradebook.groups.teachers'),
    cell: ({ getValue }) => {
      const teachers = getValue();
      if (!teachers.length) return t('gradebook.groups.noTeachers');
      return teachers.map((t) => `${t.firstName} ${t.lastName}`).join(', ');
    },
  }),
];

const table = useVueTable({
  get data() {
    return props.group.subjects;
  },
  getRowId: (gs) => gs.id.toString(),
  columns,
  getCoreRowModel: getCoreRowModel(),
});
</script>

<template>
  <div class="rounded-lg border bg-card text-card-foreground">
    <div class="border-b px-6 py-4">
      <h3 class="text-lg font-semibold leading-none tracking-tight">
        {{ group.name }}
        <span class="text-sm font-normal text-muted-foreground">({{ group.shortcut }})</span>
      </h3>
    </div>
    <div class="pt-0">
      <TableTemplate :table="table">
        <template #row="{ row }">
          <TableRow class="cursor-pointer" @click="emit('edit-subject', row.original)">
            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
              <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
            </TableCell>
          </TableRow>
        </template>
      </TableTemplate>
      <div class="mt-2 flex justify-center pb-2">
        <Button variant="ghost" size="sm" @click="emit('add-subject')">
          <LucidePlus class="size-4" />
          {{ t('gradebook.groups.addSubject') }}
        </Button>
      </div>
    </div>
  </div>
</template>
