<script setup lang="ts">
import SchoolComplexDialogForm from "./SchoolComplexDialogForm.vue";
import { LucideLoader2, LucidePencil } from "lucide-vue-next";
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

import type { SchoolComplexEntity } from "@/api/entities/school-structure";
import SchoolStructureService from "@/api/services/school-structure";
import type { SchoolComplexForm } from "@/types";

const { t } = useI18n();
const showDialog = ref(false);
const { currentData } = defineProps<{ currentData: SchoolComplexEntity }>();
const emit = defineEmits(["edited"]);

const isLoading = ref(false);
const error = ref<string | null>(null);

async function onSubmit(values: SchoolComplexForm) {
    isLoading.value = true;
    error.value = null;
    try {
        await SchoolStructureService.editSchoolComplexData(values, currentData.id);
        emit("edited");
        showDialog.value = false;
    } catch {
        error.value = "unknownError";
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger as-child>
            <Button variant="secondary">
                <LucidePencil aria-hidden="true" />
                <span class="not-sm:hidden">{{ t("editComplexData") }}</span>
            </Button>
        </DialogTrigger>
        <DialogContent class="!max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>{{ t("editComplexData") }}</DialogTitle>
                <DialogDescription>{{ t("requiredFieldsInfo") }}</DialogDescription>
            </DialogHeader>
            <SchoolComplexDialogForm
                :loading="isLoading"
                :error-message="error"
                @submit="onSubmit"
                :current-data="currentData"
            >
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
