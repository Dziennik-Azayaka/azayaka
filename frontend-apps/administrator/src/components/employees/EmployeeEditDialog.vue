<script setup lang="ts">
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
    DialogTrigger,
} from "@azayaka-frontend/ui";

import type { EmployeeEntity } from "@/api/entities/employee";
import { ApiError } from "@/api/error";
import EmployeeService from "@/api/services/employee";
import EmployeeArchiveDialog from "@/components/employees/EmployeeArchiveDialog.vue";
import EmployeeDialogForm from "@/components/employees/EmployeeDialogForm.vue";
import type { EmployeeForm } from "@/types";

const props = defineProps<{ currentData: EmployeeEntity }>();

const { t } = useI18n();
const queryClient = useQueryClient();

const showDialog = ref(false);
const error = ref<string | null>(null);

const { isPending, mutate: onSubmit } = useMutation({
    mutationKey: ["editEmployee"],
    mutationFn: async (form: EmployeeForm) => {
        await EmployeeService.editEmployeeData(props.currentData.id, form);
    },
    onSuccess: async () => {
        showDialog.value = false;
        await queryClient.invalidateQueries({ queryKey: ["employees"] });
    },
    onError: (reason) => {
        error.value = reason instanceof ApiError ? reason.getTranslationId() : "unknownError";
    },
});

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
                :loading="isPending"
                @submit="onSubmit"
            >
                <template #footer>
                    <DialogFooter>
                        <EmployeeArchiveDialog :archived="!currentData.active" :employee-id="currentData.id" />
                        <div class="flex-1" />
                        <DialogClose as-child>
                            <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                        </DialogClose>
                        <Button type="submit">
                            <template v-if="!isPending">{{ t("save") }}</template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                    </DialogFooter>
                </template>
            </EmployeeDialogForm>
        </DialogContent>
    </Dialog>
</template>
