<script setup lang="ts">
import SchoolComplexDialogForm from "./SchoolComplexDialogForm.vue";
import { LucideLoader2 } from "lucide-vue-next";
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
} from "@azayaka-frontend/ui";

import SchoolStructureService from "@/api/services/school-structure";
import type { SchoolComplexForm } from "@/types";

const { t } = useI18n();
const model = defineModel({ default: false });
const emit = defineEmits(["created"]);

const isLoading = ref(false);
const error = ref<string | null>(null);

async function onSubmit(values: SchoolComplexForm) {
    isLoading.value = true;
    error.value = null;
    try {
        await SchoolStructureService.createSchoolComplex(values);
        emit("created");
        model.value = false;
    } catch {
        error.value = "unknownError";
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <Dialog v-model:open="model">
        <DialogContent class="!max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>{{ t("createSchoolComplex") }}</DialogTitle>
                <DialogDescription>{{ t("requiredFieldsInfo") }}</DialogDescription>
            </DialogHeader>
            <SchoolComplexDialogForm @submit="onSubmit" :error-message="error" :loading="isLoading">
                <template #footer>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                        </DialogClose>
                        <Button type="submit">
                            <template v-if="!isLoading">{{ t("save") }}</template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                    </DialogFooter>
                </template>
            </SchoolComplexDialogForm>
        </DialogContent>
    </Dialog>
</template>
