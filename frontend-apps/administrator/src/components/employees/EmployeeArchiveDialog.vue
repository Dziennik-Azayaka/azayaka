<script setup lang="ts">
import ErrorBanner from "#ui/components/ui/banner/ErrorBanner.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { LucideArchive, LucideLoader2 } from "lucide-vue-next";
import { useForm } from "vee-validate";
import { ref } from "vue";
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
} from "@azayaka-frontend/ui";

import { IncorrectPasswordError } from "@/api/errors";
import EmployeeApiService from "@/api/services/employee";

const props = defineProps<{ archived: boolean; employeeId: number }>();
const emit = defineEmits(["changed"]);

const { t } = useI18n();
const showDialog = ref(false);

const isLoading = ref(false);
const error = ref<string | null>(null);

const formSchema = toTypedSchema(
    z.object({
        password: z
            .string({
                required_error: t("requiredFieldError"),
            })
            .min(1, {
                message: t("requiredFieldError"),
            })
            .max(255, t("fieldMaxLengthError", { number: 255 })),
    }),
);

const form = useForm({
    validationSchema: formSchema,
});

const onSubmit = form.handleSubmit(async ({ password }) => {
    isLoading.value = true;
    error.value = null;

    try {
        await EmployeeApiService.changeEmployeeActivity(props.archived, props.employeeId, password);
        emit("changed");
        showDialog.value = false;
    } catch (reason) {
        if (reason instanceof IncorrectPasswordError) error.value = "incorrectPasswordError";
        else error.value = "unknownError";
    } finally {
        isLoading.value = false;
    }
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
                            <PasswordInput v-bind="componentField" :disabled="isLoading" />
                        </FormItem>
                    </FormControl>
                </FormField>

                <ErrorBanner v-if="error" :description="t(error)" />

                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                    </DialogClose>
                    <Button @click="onSubmit" variant="destructive">
                        <template v-if="!isLoading">{{ t("confirm") }}</template>
                        <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
