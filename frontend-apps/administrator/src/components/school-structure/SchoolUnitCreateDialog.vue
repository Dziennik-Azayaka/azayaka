<script setup lang="ts">
import SchoolUnitDialogForm from "./SchoolUnitDialogForm.vue";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { LucideLoader2, LucidePlus } from "lucide-vue-next";
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
    DialogTrigger,
} from "@azayaka-frontend/ui";

import { ApiError } from "@/api/error";
import SchoolStructureService from "@/api/services/school-structure";
import type { SchoolUnitForm } from "@/types";

const props = defineProps<{ schoolComplexId: number }>();

const { t } = useI18n();
const queryClient = useQueryClient();

const showDialog = ref(false);
const error = ref<string | null>(null);

const { isPending, mutate: onSubmit } = useMutation({
    mutationKey: ["createUnit"],
    mutationFn: async (form: SchoolUnitForm) => {
        await SchoolStructureService.createSchoolUnit(form, props.schoolComplexId);
    },
    onSuccess: async () => {
        showDialog.value = false;
        await queryClient.invalidateQueries({ queryKey: ["schoolStructure"] });
    },
    onError: (reason) => {
        error.value = reason instanceof ApiError ? reason.getTranslationId() : "unknownError";
    },
});

watch(showDialog, (value) => {
    if (value) error.value = null;
});
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger as-child>
            <Button>
                <LucidePlus aria-hidden="true" />
                <span class="not-sm:hidden">{{ t("addUnitToComplex") }}</span>
            </Button>
        </DialogTrigger>
        <DialogContent class="!max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>{{ t("addUnitToComplex") }}</DialogTitle>
                <DialogDescription>{{ t("requiredFieldsInfo") }}</DialogDescription>
            </DialogHeader>
            <SchoolUnitDialogForm :error-message="error" :loading="isPending" @submit="onSubmit">
                <template #footer>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                        </DialogClose>
                        <Button type="submit">
                            <template v-if="!isPending">{{ t("add") }}</template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                    </DialogFooter>
                </template>
            </SchoolUnitDialogForm>
        </DialogContent>
    </Dialog>
</template>
