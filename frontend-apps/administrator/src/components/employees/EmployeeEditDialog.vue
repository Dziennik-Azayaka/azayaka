<script setup lang="ts">
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
    DialogTrigger,
} from "@azayaka-frontend/ui";

import type { EmployeeEntity } from "@/api/entities/employee";
import EmployeeService from "@/api/services/employee";
import EmployeeArchiveDialog from "@/components/employees/EmployeeArchiveDialog.vue";
import EmployeeDialogForm from "@/components/employees/EmployeeDialogForm.vue";
import type { EmployeeForm } from "@/types";
import { TakenShortcutError } from "@/api/errors.ts";

const { t } = useI18n();
const showDialog = ref(false);
const isLoading = ref(false);
const error = ref<string | null>(null);
const emit = defineEmits(["edited"]);
const { currentData } = defineProps<{ currentData: EmployeeEntity }>();

async function onSubmit(values: EmployeeForm) {
    isLoading.value = true;
    error.value = null;
    try {
        await EmployeeService.editEmployeeData(currentData.id, values);
        emit("edited");
        showDialog.value = false;
    } catch (reason) {
        if (reason instanceof TakenShortcutError) error.value = 'takenShortcutError';
        else error.value = "unknownError";
    } finally {
        isLoading.value = false;
    }
}

watch(showDialog, (value) => {
    if (value) error.value = null;
});

const mapEmployeeToForm = (employee: EmployeeEntity): EmployeeForm => ({
    firstName: employee.firstName,
    lastName: employee.lastName,
    shortcut: employee.shortcut,
    roles: [...employee.roles],
});
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>
        <DialogContent class="!max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>{{ t("editEmployee") }}</DialogTitle>
                <DialogDescription>{{ t("requiredFieldsInfo") }}</DialogDescription>
            </DialogHeader>
            <EmployeeDialogForm
                :current-data="mapEmployeeToForm(currentData)"
                :error-message="error"
                :loading="isLoading"
                @submit="onSubmit"
            >
                <template #footer>
                    <DialogFooter>
                        <EmployeeArchiveDialog
                            :archived="!currentData.active"
                            :employee-id="currentData.id"
                            @changed="emit('edited')"
                        />
                        <div class="flex-1" />
                        <DialogClose as-child>
                            <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                        </DialogClose>
                        <Button type="submit">
                            <template v-if="!isLoading">{{ t("save") }}</template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                    </DialogFooter>
                </template>
            </EmployeeDialogForm>
        </DialogContent>
    </Dialog>
</template>
