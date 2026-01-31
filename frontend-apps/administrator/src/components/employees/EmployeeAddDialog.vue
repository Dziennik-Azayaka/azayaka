<script setup lang="ts">
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
import EmployeeService from "@/api/services/employee";
import EmployeeDialogForm from "@/components/employees/EmployeeDialogForm.vue";
import type { EmployeeForm } from "@/types";

const { t } = useI18n();
const queryClient = useQueryClient();

const showDialog = ref(false);
const error = ref<string | null>(null);

watch(showDialog, (value) => {
    if (value) error.value = null;
});

const { isPending, mutate: onSubmit } = useMutation({
    mutationKey: ["addEmployee"],
    mutationFn: async (form: EmployeeForm) => {
        await EmployeeService.addEmployee(form);
    },
    onSuccess: async () => {
        showDialog.value = false;
        await queryClient.invalidateQueries({ queryKey: ["employees"] });
    },
    onError: (reason) => {
        error.value = reason instanceof ApiError ? reason.getTranslationId() : "unknownError";
    },
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
            <EmployeeDialogForm :error-message="error" :loading="isPending" @submit="onSubmit">
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
            </EmployeeDialogForm>
        </DialogContent>
    </Dialog>
</template>
