<script lang="ts" setup generic="T">
import Table from './Table.vue';
import TableBody from './TableBody.vue';
import TableCell from './TableCell.vue';
import TableContainer from './TableContainer.vue';
import TableHead from './TableHead.vue';
import TableHeader from './TableHeader.vue';
import TableRow from './TableRow.vue';
import TableTemplateRow from './TableTemplateRow.vue';
import { FlexRender, type Row, type Table as TableType } from '@tanstack/vue-table';
import { h } from 'vue';
import { useI18n } from 'vue-i18n';

defineProps<{ table: TableType<T> }>();

const { t } = useI18n();

const templateRow = (row: Row<T>) => h(TableTemplateRow<T>, { row });
</script>

<template>
  <TableContainer>
    <Table>
      <TableHeader>
        <TableRow
          v-for="(headerGroup, groupIndex) in table.getHeaderGroups()"
          :key="headerGroup.id"
        >
          <TableHead
            v-for="header in headerGroup.headers"
            :key="header.id"
            :colspan="header.colSpan"
            :class="{
              'bg-background!': groupIndex + 1 !== table.getHeaderGroups().length,
              'w-0': header.column.id === 'select',
            }"
          >
            <FlexRender
              v-if="!header.isPlaceholder"
              :props="header.getContext()"
              :render="header.column.columnDef.header"
            />
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <template v-if="table.getRowModel().rows.length">
          <template v-for="row in table.getRowModel().rows" :key="row.id">
            <slot name="row" :templateRow="templateRow(row)" :row="row">
              <component :is="templateRow(row)" />
            </slot>
          </template>
        </template>
        <TableRow v-else>
          <TableCell
            :colspan="table.getVisibleFlatColumns().length"
            class="h-18 text-center text-foreground/70"
          >
            {{ t('common.table.empty') }}
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </TableContainer>
</template>
