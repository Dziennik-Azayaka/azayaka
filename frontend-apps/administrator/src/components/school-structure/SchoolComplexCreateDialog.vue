<script setup lang="ts">
import SchoolComplexDialogForm from "./SchoolComplexDialogForm.vue";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { LucideLoader2 } from "lucide-vue-next";
import { ref, watch } from "vue";
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
} from "@azayaka-frontend/ui";

import { ApiError } from "@/api/error";
import SchoolStructureService from "@/api/services/school-structure";
import type { SchoolComplexForm } from "@/types";

const model = defineModel({ default: false });

const { t } = useI18n();
const queryClient = useQueryClient();

const error = ref<string | null>(null);

const { isPending, mutate: onSubmit } = useMutation({
    mutationKey: ["createUnitComplex"],
    mutationFn: async (form: SchoolComplexForm) => {
        await SchoolStructureService.createSchoolComplex(form);
    },
    onSuccess: async () => {
        model.value = false;
        await queryClient.invalidateQueries({ queryKey: ["schoolStructure"] });
    },
    onError: (reason) => {
        error.value = reason instanceof ApiError ? reason.getTranslationId() : "unknownError";
    },
});

watch(model, (value) => {
    if (value) error.value = null;
});
</script>

<template>
    <Dialog v-model:open="model">
        <DialogContent class="!max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>{{ t("createSchoolComplex") }}</DialogTitle>
                <DialogDescription>{{ t("requiredFieldsInfo") }}</DialogDescription>
            </DialogHeader>
            <SchoolComplexDialogForm @submit="onSubmit" :error-message="error" :loading="isPending">
                <template #footer>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                        </DialogClose>
                        <Button type="submit">
                            <template v-if="!isPending">{{ t("save") }}</template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                    </DialogFooter>
                </template>
            </SchoolComplexDialogForm>
        </DialogContent>
    </Dialog>
</template>
