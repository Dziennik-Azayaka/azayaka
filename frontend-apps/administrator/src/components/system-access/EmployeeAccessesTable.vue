<script setup lang="ts">
import { Checkbox } from "#ui/components/ui/checkbox";
import { Input } from "#ui/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "#ui/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "#ui/components/ui/table";
import {
    FlexRender,
    createColumnHelper,
    getCoreRowModel,
    getFilteredRowModel,
    getSortedRowModel,
    useVueTable,
} from "@tanstack/vue-table";
import { h } from "vue";
import { useI18n } from "vue-i18n";

import { AccessStatus as AccessStatusEnum } from "@/api/entities/access.ts";
import type { EmployeeAccessEntity } from "@/api/entities/employee-access.ts";
import AccessInfoDialog from "@/components/system-access/AccessInfoDialog.vue";
import AccessStatusBadge from "@/components/system-access/AccessStatusBadge.vue";

const { accesses } = defineProps<{ accesses: EmployeeAccessEntity[] }>();
const { t, d } = useI18n();

const columnHelper = createColumnHelper<EmployeeAccessEntity>();
const columns = [
    columnHelper.display({
        id: "select",
        header: ({ table }) =>
            h(Checkbox, {
                modelValue: table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && "indeterminate"),
                "onUpdate:modelValue": (value) => table.toggleAllPageRowsSelected(!!value),
                ariaLabel: "Select all",
            }),
        cell: ({ row }) =>
            h(Checkbox, {
                modelValue: row.getIsSelected(),
                "onUpdate:modelValue": (value) => row.toggleSelected(!!value),
                ariaLabel: "Select row",
                onClick: (event: Event) => event.stopPropagation(),
            }),
        enableSorting: false,
        enableHiding: false,
    }),
    columnHelper.accessor((row) => `${row.fullName} (${row.shortcut})`, {
        id: "fullName",
        header: () => `${t("fullName")} (${t("short").toLocaleLowerCase()})`,
    }),
    columnHelper.accessor("status", {
        header: () => t("accessStatus"),
        cell: ({ row }) => h(AccessStatusBadge, { status: row.getValue("status") as AccessStatusEnum }),
        filterFn: (row, columnId, filterValue) => filterValue === "all" || row.getValue(columnId) === filterValue,
    }),
    columnHelper.accessor((row) => (row.status === AccessStatusEnum.ACTIVE ? d(row.lastLoginAt, "long") : "-"), {
        id: "lastLoginAt",
        header: () => t("lastLoginDate"),
    }),
];

const table = useVueTable({
    data: accesses,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
    state: {
        sorting: [
            {
                id: "fullName",
                desc: false,
            },
        ],
    },
});
</script>

<template>
    <div class="flex flex-wrap gap-3 items-center py-4">
        <Input
            :aria-label="t('searchForEmployees')"
            :model-value="table.getColumn('fullName')?.getFilterValue() as string"
            :placeholder="t('searchForEmployees')"
            class="max-w-sm"
            @update:model-value="table.getColumn('fullName')?.setFilterValue($event)"
        />
        <div class="flex-1" />
        <Select
            :model-value="table.getColumn('status')?.getFilterValue() ?? 'all'"
            :aria-label="t('accessStatus')"
            @update:model-value="table.getColumn('status')?.setFilterValue($event)"
        >
            <SelectTrigger>
                <span class="text-muted-foreground">{{ t("accessStatus") }}: </span>
                <SelectValue />
            </SelectTrigger>
            <SelectContent>
                <SelectItem value="all">{{ t("accessStatus.all") }}</SelectItem>
                <SelectItem value="unactive">{{ t("accessStatus.unactive") }}</SelectItem>
                <SelectItem value="codeGenerated">{{ t("accessStatus.codeGenerated") }}</SelectItem>
                <SelectItem value="active">{{ t("accessStatus.active") }}</SelectItem>
            </SelectContent>
        </Select>
    </div>
    <div class="rounded-md border overflow-hidden">
        <Table>
            <TableHeader>
                <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                    <TableHead
                        v-for="header in headerGroup.headers"
                        :key="header.id"
                        :class="{ 'w-0': header.id === 'select' }"
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
                    <AccessInfoDialog
                        v-for="row in table.getRowModel().rows"
                        :key="row.id"
                        :data="row.original"
                        user-role="employee"
                    >
                        <TableRow class="cursor-pointer">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                <FlexRender :props="cell.getContext()" :render="cell.column.columnDef.cell" />
                            </TableCell>
                        </TableRow>
                    </AccessInfoDialog>
                </template>
                <TableRow v-else>
                    <TableCell :colspan="columns.length" class="h-18 text-center text-foreground/70">
                        {{ t("noResults") }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
    <pre class="p-5 rounded-md bg-accent font-mono text-xs mt-5 border">{{ accesses }}</pre>
</template>
