<script lang="ts" setup generic="T">
import TableCell from './TableCell.vue';
import TableRow from './TableRow.vue';
import { FlexRender, type Row } from '@tanstack/vue-table';

defineProps<{ row: Row<T> }>();
</script>

<template>
  <TableRow class="cursor-pointer">
    <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
      <FlexRender :props="cell.getContext()" :render="cell.column.columnDef.cell" />
    </TableCell>
  </TableRow>
</template>
