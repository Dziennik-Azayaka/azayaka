<script lang="ts" setup>
import { TableRow } from "#ui/components/ui/table";
import {
    FlexRender,
    createColumnHelper,
    getCoreRowModel,
    getFilteredRowModel,
    getSortedRowModel,
    useVueTable,
} from "@tanstack/vue-table";
import { useI18n } from "vue-i18n";

import { Table, TableBody, TableCell, TableHead, TableHeader } from "@azayaka-frontend/ui";

import type { SubjectEntity } from "@/api/entities/subject.ts";

const props = defineProps<{ subjects: SubjectEntity[] }>();
const emit = defineEmits(["refreshNeeded"]);
const { t } = useI18n();

const columnHelper = createColumnHelper<SubjectEntity>();
const columns = [
    columnHelper.accessor("name", {
        header: () => `${t("name")}`,
    }),
    columnHelper.accessor("shortcut", {
        header: () => `${t("short")}`,
    }),
    columnHelper.accessor("active", {
        header: () => `${t("activeSubject")}`
    }),
];

const table = useVueTable({
    data: props.subjects,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
    state: {
        sorting: [
            {
                id: "name",
                desc: false,
            },
        ],
    },
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
                    <template v-if="table.getRowModel().rows.length">
                        <template
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                        >
                            <TableRow>
                                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                    <FlexRender :props="cell.getContext()" :render="cell.column.columnDef.cell" />
                                </TableCell>
                            </TableRow>
                        </template>
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
