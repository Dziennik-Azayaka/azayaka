<script setup lang="ts">
import SchoolUnitDialogForm from "./SchoolUnitDialogForm.vue";
import { LucideLoader2, LucidePencil } from "lucide-vue-next";
import { ref, watch } from "vue";
import { useI18n } from "vue-i18n";

import {
    Button,
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@azayaka-frontend/ui";

import type { SchoolUnitEntity } from "@/api/entities/school-structure";
import SchoolStructureService from "@/api/services/school-structure";
import type { SchoolUnitForm } from "@/types";

const { t } = useI18n();
const showDialog = ref(false);
const props = defineProps<{ currentData: SchoolUnitEntity }>();
const emit = defineEmits(["edited"]);

const isLoading = ref(false);
const error = ref<string | null>(null);

async function onSubmit(values: SchoolUnitForm) {
    console.log(values);
    isLoading.value = true;
    error.value = null;
    try {
        await SchoolStructureService.editSchoolUnitData(
            values,
            props.currentData.id,
            props.currentData.schoolComplexId,
        );
        emit("edited");
        showDialog.value = false;
    } catch {
        error.value = "unknownError";
    } finally {
        isLoading.value = false;
    }
}

const onClose = () => {
    showDialog.value = false;
};

watch(showDialog, (value) => {
    if (value) error.value = null;
});
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger as-child>
            <Button variant="outline">
                <LucidePencil aria-hidden="true" />
                <span class="not-sm:hidden">{{ t("editUnitData") }}</span>
            </Button>
        </DialogTrigger>
        <DialogContent class="!max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>{{ t("editUnitData") }}</DialogTitle>
                <DialogDescription>{{ t("requiredFieldsInfo") }}</DialogDescription>
            </DialogHeader>
            <SchoolUnitDialogForm
                :current-data="currentData"
                :error-message="error"
                :loading="isLoading"
                @submit="onSubmit"
            >
                <template #footer>
                    <DialogFooter>
                        <Button variant="outline" type="button" @click="onClose">{{ t("cancel") }}</Button>
                        <Button type="submit">
                            <template v-if="!isLoading">{{ t("save") }}</template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                    </DialogFooter>
                </template>
            </SchoolUnitDialogForm>
        </DialogContent>
    </Dialog>
</template>
