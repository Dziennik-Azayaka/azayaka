<script lang="ts" setup>
import { TableRow } from "#ui/components/ui/table";
import { FlexRender, createColumnHelper, getCoreRowModel, useVueTable } from "@tanstack/vue-table";
import { h } from "vue";
import { useI18n } from "vue-i18n";

import { Table, TableBody, TableCell, TableHead, TableHeader } from "@azayaka-frontend/ui";

import type { Employee, EmployeeRole } from "@/api/entities/employee.ts";
import EmployeesTableRoles from "@/components/employees/EmployeesTableRoles.vue";

const { employees } = defineProps<{ employees: Employee[] }>();
const { t } = useI18n();

const columnHelper = createColumnHelper<Employee>();
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
});
</script>

<template>
    <div class="w-full">
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
                    <TableRow v-for="row in table.getRowModel().rows" :key="row.id">
                        <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                            <FlexRender :props="cell.getContext()" :render="cell.column.columnDef.cell" />
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <!-- TODO: Remove it -->
        <pre class="p-4 mt-5 rounded-md bg-accent text-foreground/70 text-xs overflow-hidden">{{ employees }}</pre>
    </div>
</template>
