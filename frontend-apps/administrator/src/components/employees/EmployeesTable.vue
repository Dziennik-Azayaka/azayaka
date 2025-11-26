<script lang="ts" setup>
import { Input } from "#ui/components/ui/input";
import { TableRow } from "#ui/components/ui/table";
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

import { Table, TableBody, TableCell, TableHead, TableHeader } from "@azayaka-frontend/ui";

import type { EmployeeEntity, EmployeeRole } from "@/api/entities/employee.ts";
import EmployeeAddDialog from "@/components/employees/EmployeeAddDialog.vue";
import EmployeeEditDialog from "@/components/employees/EmployeeEditDialog.vue";
import EmployeesTableRoles from "@/components/employees/EmployeesTableRoles.vue";

const { employees } = defineProps<{ employees: EmployeeEntity[] }>();
const emit = defineEmits(["refreshNeeded"]);
const { t } = useI18n();

const columnHelper = createColumnHelper<EmployeeEntity>();
const columns = [
    columnHelper.accessor((row) => `${row.lastName} ${row.firstName} (${row.shortcut})`, {
        id: "fullName",
        header: () => `${t("fullName")} (${t("short").toLocaleLowerCase()})`,
    }),
    columnHelper.accessor("roles", {
        header: () => t("roles"),
        cell: ({ row }) => h(EmployeesTableRoles, { roles: row.getValue("roles") as Set<EmployeeRole> }),
    }),
];

const table = useVueTable({
    data: employees,
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
    <div class="w-full">
        <div class="flex items-center py-4">
            <Input
                :aria-label="t('searchForEmployees')"
                :model-value="table.getColumn('fullName')?.getFilterValue() as string"
                :placeholder="t('searchForEmployees')"
                class="max-w-sm"
                @update:model-value="table.getColumn('fullName')?.setFilterValue($event)"
            />
            <div class="flex-1" />
            <EmployeeAddDialog @added="emit('refreshNeeded')" />
        </div>
        <div class="rounded-md border overflow-hidden">
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
                    <template v-if="table.getRowModel().rows.length">
                        <EmployeeEditDialog
                            :current-data="employees[row.index]"
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            @edited="emit('refreshNeeded')"
                        >
                            <TableRow class="cursor-pointer">
                                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                    <FlexRender :props="cell.getContext()" :render="cell.column.columnDef.cell" />
                                </TableCell>
                            </TableRow>
                        </EmployeeEditDialog>
                    </template>
                    <TableRow v-else>
                        <TableCell :colspan="columns.length" class="h-18 text-center text-foreground/70">
                            {{ t("noResults") }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
