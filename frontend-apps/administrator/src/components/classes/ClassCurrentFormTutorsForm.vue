<script setup lang="ts">
import { useQuery } from "@tanstack/vue-query";
import { LucideLoader2 } from "lucide-vue-next";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";

import { Checkbox, Input } from "@azayaka-frontend/ui";

import EmployeeService from "@/api/services/employee";

defineProps<{ loading: boolean; error: string | null }>();
const emit = defineEmits(["submit"]);

const { t } = useI18n();

const selectedTeachers = ref(new Set<number>());
const search = ref("");

const { data: teachers, isFetching } = useQuery({
    queryKey: ["teachers"],
    queryFn: async () => {
        const employees = await EmployeeService.getAllEmployees();
        return employees.filter((employee) => employee.active && employee.roles.has("teacher"));
    },
});

const teacherList = computed(() =>
    teachers.value
        ? [...teachers.value]
              .sort((a, b) => {
                  const aSelected = selectedTeachers.value.has(a.id);
                  const bSelected = selectedTeachers.value.has(b.id);

                  if (aSelected !== bSelected) return Number(bSelected);

                  return `${a.lastName} ${a.firstName}`.localeCompare(`${b.lastName} ${b.firstName}`);
              })
              .filter(
                  (teacher) => search.value === "" || `${teacher.lastName} ${teacher.firstName}`.includes(search.value),
              )
        : [],
);

function toggleTeacher(id: number) {
    if (selectedTeachers.value.has(id)) {
        selectedTeachers.value.delete(id);
    } else {
        selectedTeachers.value.add(id);
    }
}

function onSubmit() {
    emit("submit", [...selectedTeachers.value]);
}

const canSubmit = computed(() => !!selectedTeachers.value.size);
</script>

<template>
    <form class="space-y-3" @submit.prevent="onSubmit">
        <Input :placeholder="t('searchForTeachers')" v-model="search" />
        <ul v-if="teacherList.length" class="space-y-1.5 max-h-150 overflow-y-auto">
            <li v-for="teacher in teacherList" :key="teacher.id">
                <label class="flex gap-2 items-center select-none text-sm">
                    <Checkbox @update:model-value="() => toggleTeacher(teacher.id)" :disabled="loading" />
                    {{ teacher.lastName }} {{ teacher.firstName }} ({{ teacher.shortcut }})
                </label>
            </li>
        </ul>
        <p v-else class="text-center text-muted-foreground text-sm my-8">
            <template v-if="teachers">{{ t("noResults") }}</template>
            <template v-else-if="isFetching">
                <LucideLoader2 class="animate-spin mx-auto" :aria-label="t('pleaseWait')" />
            </template>
            <template v-else>{{ t("unknownError") }}</template>
        </p>
        <slot name="footer" :can-submit="canSubmit" />
    </form>
</template>
