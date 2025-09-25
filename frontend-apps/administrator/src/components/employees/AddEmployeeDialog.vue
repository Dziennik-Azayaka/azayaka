<script setup lang="ts">
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

import EmployeeService from "@/api/services/employee";
import EmployeeDialogForm from "@/components/employees/EmployeeDialogForm.vue";
import type { EmployeeForm } from "@/types";

const { t } = useI18n();
const showDialog = ref(false);
const isLoading = ref(false);
const error = ref<string | null>(null);
const emit = defineEmits(["added"]);

async function onSubmit(values: EmployeeForm) {
    isLoading.value = true;
    error.value = null;
    console.log(values);
    try {
        await EmployeeService.addEmployee(values);
        emit("added");
        showDialog.value = false;
    } catch {
        error.value = "unknownError";
    } finally {
        isLoading.value = false;
    }
}

watch(showDialog, (value) => {
    if (value) error.value = null;
});
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger as-child>
            <Button>
                <LucidePlus aria-hidden="true" />
                <span class="not-sm:hidden">{{ t("addEmployee") }}</span>
            </Button>
        </DialogTrigger>
        <DialogContent class="!max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>{{ t("addEmployee") }}</DialogTitle>
                <DialogDescription>{{ t("requiredFieldsInfo") }}</DialogDescription>
            </DialogHeader>
            <EmployeeDialogForm :error-message="error" :loading="isLoading" @submit="onSubmit" v-if="showDialog">
                <template #footer>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                        </DialogClose>
                        <Button type="submit">
                            <template v-if="!isLoading">{{ t("add") }}</template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                    </DialogFooter>
                </template>
            </EmployeeDialogForm>
        </DialogContent>
    </Dialog>
</template>
