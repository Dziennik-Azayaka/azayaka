<script setup lang="ts">
import { LucidePencil } from "lucide-vue-next";
import { useI18n } from "vue-i18n";

import { Button } from "@azayaka-frontend/ui";

import type { ClassificationPeriodEntity } from "@/api/entities/classification-period";
import type { SchoolUnitEntity } from "@/api/entities/school-structure";

defineProps<{ unit: SchoolUnitEntity; showHeader: boolean; periods: ClassificationPeriodEntity[] }>();

const { d } = useI18n();
</script>

<template>
    <div class="border rounded-md overflow-hidden text-sm mt-5">
        <table class="w-full">
            <thead>
                <tr v-if="showHeader">
                    <th colspan="3" class="px-3 sm:px-5 py-3 text-left">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-base">{{ unit.name }}</span>
                            <Button variant="secondary">
                                <LucidePencil />
                                Zmień
                            </Button>
                        </div>
                    </th>
                </tr>
                <tr class="*:font-medium *:px-3 *:sm:px-5 *:py-3 text-left bg-accent not-first:border-t">
                    <th>Numer</th>
                    <th>Data od</th>
                    <th>Data do</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!periods.length">
                    <td colspan="3" class="px-3 sm:px-5 py-3.5 text-center text-muted-foreground border-t">
                        Brak okresów
                    </td>
                </tr>
                <tr v-else class="border-t *:px-3 *:sm:px-5 *:py-3" v-for="period in periods" :key="period.id">
                    <td>{{ period.number }}.</td>
                    <td>{{ d(period.start, "short") }}</td>
                    <td>{{ d(period.end, "short") }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
