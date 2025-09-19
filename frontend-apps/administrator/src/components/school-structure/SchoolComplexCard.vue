<script setup lang="ts">
import SchoolComplexEditDialog from "./SchoolComplexEditDialog.vue";
import SchoolUnitCard from "./SchoolUnitCard.vue";
import SchoolUnitAddDialog from "./SchoolUnitCreateDialog.vue";

import type { SchoolComplexEntity } from "@/api/entities/school-structure";

const { data } = defineProps<{ data: SchoolComplexEntity }>();
const emit = defineEmits(["structureChanged"]);
</script>

<template>
    <li class="border rounded-md overflow-hidden not-sm:px-3 p-5 space-y-4">
        <div class="flex justify-between not-sm:flex-col sm:items-center gap-3">
            <h2 class="font-semibold">{{ data.name }}</h2>
            <div class="flex gap-3">
                <SchoolUnitAddDialog :school-complex-id="data.id" @created="emit('structureChanged')" />
                <SchoolComplexEditDialog :current-data="data" @edited="emit('structureChanged')" />
            </div>
        </div>

        <ul class="space-y-6">
            <SchoolUnitCard v-for="unit in data.units" :key="unit.id" :data="unit" @edited="emit('structureChanged')" />
        </ul>
    </li>
</template>
