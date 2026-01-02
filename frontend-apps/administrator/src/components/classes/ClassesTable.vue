<script lang="ts" setup>
import { TableRow } from "#ui/components/ui/table";
import {
    FlexRender,
    createColumnHelper,
    getCoreRowModel,
    getFacetedUniqueValues,
    getFilteredRowModel,
    getSortedRowModel,
    useVueTable,
} from "@tanstack/vue-table";
import { useI18n } from "vue-i18n";

import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
} from "@azayaka-frontend/ui";

import type { ClassEntity } from "@/api/entities/class.ts";
import { schoolYearString } from "@/utils.ts";

const props = defineProps<{ classes: ClassEntity[]; unitShorts: Map<number, string>; showCurrentLevel: boolean }>();
const emit = defineEmits(["refreshNeeded"]);
const { t } = useI18n();

const columnHelper = createColumnHelper<ClassEntity>();
const columns = [
    columnHelper.accessor("startingSchoolYear", {
        header: () => t("startingSchoolYear"),
        cell: ({ row }) => schoolYearString(row.original.startingSchoolYear),
        filterFn: (row, columnId, filterValue) => filterValue === "all" || row.getValue(columnId) === filterValue,
    }),
    props.showCurrentLevel &&
        columnHelper.accessor("level", {
            header: () => t("currentLevel"),
        }),
    columnHelper.accessor("mark", {
        header: () => t("mark"),
    }),
    columnHelper.accessor("alias", {
        header: () => t("alias"),
    }),
    columnHelper.accessor("schoolUnitId", {
        header: () => t("schoolUnit"),
        cell: ({ row }) => props.unitShorts.get(row.original.schoolUnitId),
        filterFn: (row, columnId, filterValue) => filterValue === "all" || row.getValue(columnId) === filterValue,
    }),
].filter((col) => !!col);

const table = useVueTable({
    data: props.classes,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
    state: {
        sorting: [
            {
                id: "startingSchoolYear",
                desc: true,
            },
        ],
    },
    getFacetedUniqueValues: getFacetedUniqueValues(),
});
</script>

<template>
    <section class="flex flex-wrap gap-3 items-center py-4">
        <div class="flex-1"></div>
        <Select
            :model-value="table.getColumn('startingSchoolYear')?.getFilterValue() ?? 'all'"
            :aria-label="t('startingSchoolYear')"
            @update:model-value="table.getColumn('startingSchoolYear')?.setFilterValue($event)"
        >
            <SelectTrigger class="not-lg:w-full">
                <span class="space-x-0.5">
                    <span class="text-muted-foreground">{{ t("startingSchoolYear") }}: </span>
                    <SelectValue />
                </span>
            </SelectTrigger>
            <SelectContent>
                <SelectItem value="all">{{ t("startingSchoolYear.all") }}</SelectItem>
                <SelectItem
                    v-for="id in [...table.getColumn('startingSchoolYear')!.getFacetedUniqueValues().keys()]
                        .sort()
                        .reverse()"
                    :key="id"
                    :value="id"
                >
                    {{ schoolYearString(id) }}
                </SelectItem>
            </SelectContent>
        </Select>
        <Select
            :model-value="table.getColumn('schoolUnitId')?.getFilterValue() ?? 'all'"
            :aria-label="t('schoolUnit')"
            @update:model-value="table.getColumn('schoolUnitId')?.setFilterValue($event)"
        >
            <SelectTrigger class="not-lg:w-full">
                <span class="space-x-0.5">
                    <span class="text-muted-foreground">{{ t("schoolUnit") }}: </span>
                    <SelectValue />
                </span>
            </SelectTrigger>
            <SelectContent>
                <SelectItem value="all">{{ t("schoolUnits.all") }}</SelectItem>
                <SelectItem v-for="[id, short] in unitShorts" :key="id" :value="id">{{ short }}</SelectItem>
            </SelectContent>
        </Select>
    </section>
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
                        <TableRow class="cursor-pointer" v-for="row in table.getRowModel().rows" :key="row.id">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                <FlexRender :props="cell.getContext()" :render="cell.column.columnDef.cell" />
                            </TableCell>
                        </TableRow>
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
