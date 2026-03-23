<script setup lang="ts">
import ClassCurrentFormTutorsForm from "./ClassCurrentFormTutorsForm.vue";
import ClassGeneraInfoForm from "./ClassGeneraInfoForm.vue";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { asyncComputed } from "@vueuse/core";
import { LucideLoader2, LucidePlus } from "lucide-vue-next";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import {
    Button,
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@azayaka-frontend/ui";

import type { SchoolUnitEntity } from "@/api/entities/school-structure";
import { ApiError } from "@/api/error";
import ClassService from "@/api/services/class";
import type { ClassGeneralInfoForm } from "@/types";

const props = defineProps<{ unitsPromise: Promise<SchoolUnitEntity[]> }>();

const { t } = useI18n();
const queryClient = useQueryClient();

const open = ref(false);
const error = ref<string | null>(null);
const state = ref<
    { step: "generalInfo"; values?: ClassGeneralInfoForm } | { step: "formTutors"; values: ClassGeneralInfoForm }
>({
    step: "generalInfo",
});

const units = asyncComputed(async () => (await props.unitsPromise).filter((unit) => !unit.archived));

function onGeneralInfoSubmit(values: ClassGeneralInfoForm) {
    state.value = { step: "formTutors", values: values };
}

function onUpdateOpen(open: boolean) {
    if (!open) return;
    state.value = { step: "generalInfo" };
}

function backToGeneralInfo() {
    state.value = { step: "generalInfo", values: state.value.values };
}

const { mutate: save, isPending } = useMutation({
    mutationKey: ["addClass"],
    mutationFn: async (teacherIds: number[]) => {
        if (state.value.step === "generalInfo") return;
        await ClassService.addClass(state.value.values, teacherIds);
        await queryClient.invalidateQueries({ queryKey: ["classes"] });
        open.value = false;
    },
    onError: (reason) => {
        error.value = reason instanceof ApiError ? reason.getTranslationId() : "unknownError";
    },
});
</script>

<template>
    <Dialog v-model:open="open" @update:open="onUpdateOpen">
        <DialogTrigger>
            <Button>
                <LucidePlus />
                {{ t("addClass") }}
            </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t(state.step === "generalInfo" ? "addClass" : "addFormTutors") }}</DialogTitle>
                <DialogDescription>{{
                    t(state.step === "generalInfo" ? "requiredFieldsInfo" : "atLeastOneFormTutorInfo")
                }}</DialogDescription>
            </DialogHeader>
            <ClassGeneraInfoForm
                @submit="(values) => onGeneralInfoSubmit(values)"
                v-if="state.step === 'generalInfo'"
                :school-units="units ?? []"
                :loading="false"
                :current-data="state.values"
            >
                <template #footer>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                        </DialogClose>
                        <Button type="submit">
                            {{ t("continue") }}
                        </Button>
                    </DialogFooter>
                </template>
            </ClassGeneraInfoForm>
            <ClassCurrentFormTutorsForm
                v-else
                @submit="(teacherIds) => save(teacherIds)"
                :loading="isPending"
                :error="error"
            >
                <template #footer="{ canSubmit }">
                    <DialogFooter>
                        <Button variant="outline" type="button" @click="backToGeneralInfo">{{ t("back") }}</Button>
                        <Button type="submit" :disabled="isPending || !canSubmit">
                            <template v-if="!isPending">{{ t("save") }}</template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                    </DialogFooter>
                </template>
            </ClassCurrentFormTutorsForm>
        </DialogContent>
    </Dialog>
</template>
