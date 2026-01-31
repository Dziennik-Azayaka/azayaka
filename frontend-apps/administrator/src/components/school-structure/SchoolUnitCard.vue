<script setup lang="ts">
import SchoolUnitArchiveDialog from "./SchoolUnitArchiveDialog.vue";
import SchoolUnitEditDialog from "./SchoolUnitEditDialog.vue";
import { useI18n } from "vue-i18n";

import type { SchoolUnitEntity } from "@/api/entities/school-structure";
import InstitutionTypes from "@/resources/institution-types.json";
import StudentCategories from "@/resources/student-categories.json";
import Voivodeships from "@/resources/voivodeships.json";

const { t } = useI18n();
defineProps<{ data: SchoolUnitEntity }>();
</script>

<template>
    <li class="border rounded-md shadow-xs overflow-hidden">
        <div class="px-3 sm:px-5 py-4 flex not-sm:flex-col justify-between sm:items-center gap-2 bg-accent">
            <h2 class="font-semibold" :class="{ italic: data.archived }">
                {{ data.name }} ({{ data.shortName }})
                <template v-if="data.archived">({{ t("archivedUnit") }})</template>
            </h2>
            <div class="flex gap-3">
                <SchoolUnitEditDialog :current-data="data" v-if="!data.archived" />
                <SchoolUnitArchiveDialog :archived="data.archived" :unit-id="data.id" />
            </div>
        </div>
        <dl :class="{ 'opacity-75': data.archived }">
            <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
                <dt class="text-sm/6 font-medium">{{ t("institutionType") }}</dt>
                <dd class="text-sm/6 text-foreground/80">
                    {{ t(InstitutionTypes.find(({ id }) => id === data.typeId)!.nameTranslationId) }}
                </dd>
            </div>
            <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
                <dt class="text-sm/6 font-medium">{{ t("voivodeship") }}</dt>
                <dd class="text-sm/6 text-foreground/80">
                    {{ Voivodeships.find(({ id }) => id === data.voivodeshipId)!.name }}
                </dd>
            </div>
            <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
                <dt class="text-sm/6 font-medium">{{ t("commune") }}</dt>
                <dd class="text-sm/6 text-foreground/80">{{ data.commune }}</dd>
            </div>
            <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
                <dt class="text-sm/6 font-medium">{{ t("district") }}</dt>
                <dd class="text-sm/6 text-foreground/80">{{ data.district || "-" }}</dd>
            </div>
            <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
                <dt class="text-sm/6 font-medium">{{ t("address") }}</dt>
                <dd class="text-sm/6 text-foreground/80">{{ data.address }}</dd>
            </div>
            <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
                <dt class="text-sm/6 font-medium">{{ t("studentCategory") }}</dt>
                <dd class="text-sm/6 text-foreground/80">
                    {{ t(StudentCategories.find(({ value }) => value === data.studentCategory)!.nameTranslationId) }}
                </dd>
            </div>
        </dl>
    </li>
</template>
