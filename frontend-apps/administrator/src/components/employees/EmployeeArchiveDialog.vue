<script setup lang="ts">
import ErrorBanner from "#ui/components/ui/banner/ErrorBanner.vue";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { toTypedSchema } from "@vee-validate/zod";
import { LucideArchive, LucideLoader2 } from "lucide-vue-next";
import { useForm } from "vee-validate";
import { ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import z from "zod";

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
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    PasswordInput,
    requiredStringField,
} from "@azayaka-frontend/ui";

import { ApiError } from "@/api/error";
import EmployeeService from "@/api/services/employee";

const props = defineProps<{ archived: boolean; employeeId: number }>();

const { t } = useI18n();
const queryClient = useQueryClient();

const showDialog = ref(false);
const error = ref<string | null>(null);

const formSchema = toTypedSchema(
    z.object({
        password: requiredStringField(t),
    }),
);

const form = useForm({
    validationSchema: formSchema,
});

const onSubmit = form.handleSubmit(async ({ password }) => mutate(password));

const { isPending, mutate } = useMutation({
    mutationKey: ["archiveEmployee"],
    mutationFn: async (password: string) => {
        await EmployeeService.changeEmployeeActivity(props.archived, props.employeeId, password);
    },
    onSuccess: async () => {
        await queryClient.invalidateQueries({ queryKey: ["employees"] });
        showDialog.value = false;
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
            <Button :variant="archived ? 'outline' : 'destructive'">
                <LucideArchive aria-hidden="true" />
                <template v-if="!archived">{{ t("moveToArchive") }}</template>
                <template v-else>{{ t("unarchive") }}</template>
            </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    {{ t(archived ? "unarchiveEmployeeDialogTitle" : "archiveEmployeeDialogTitle") }}
                </DialogTitle>
                <DialogDescription>
                    {{ t(archived ? "unarchiveEmployeeDialogDescription" : "archiveEmployeeDialogDescription") }}
                </DialogDescription>
            </DialogHeader>
            <form @submit="onSubmit" class="space-y-3">
                <FormField name="password" v-slot="{ componentField }">
                    <FormLabel>{{ t("password") }}</FormLabel>
                    <FormControl>
                        <FormItem>
                            <PasswordInput v-bind="componentField" :disabled="isPending" />
                        </FormItem>
                    </FormControl>
                </FormField>

                <ErrorBanner v-if="error" :description="t(error)" />

                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                    </DialogClose>
                    <Button @click="onSubmit" variant="destructive">
                        <template v-if="!isPending">{{ t("confirm") }}</template>
                        <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
