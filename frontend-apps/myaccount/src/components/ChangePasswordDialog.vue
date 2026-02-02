<script setup lang="ts">
import ErrorBanner from "#ui/components/ui/banner/ErrorBanner.vue";
import { useMutation } from "@tanstack/vue-query";
import { toTypedSchema } from "@vee-validate/zod";
import { LucideLoader2 } from "lucide-vue-next";
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
    FormMessage,
    PasswordInput,
} from "@azayaka-frontend/ui";

import { ApiError } from "@/api/error";
import UserApiService from "@/api/services/user";

const { t } = useI18n();

const formSchema = toTypedSchema(
    z
        .object({
            currentPassword: z.string({
                required_error: t("requiredPasswordError"),
            }),
            newPassword: z
                .string({
                    required_error: t("requiredPasswordError"),
                })
                .min(8, t("passwordMinLengthError"))
                .max(128, t("passwordMaxLengthError")),
            repeatPassword: z.string({
                required_error: t("requiredPasswordError"),
            }),
        })
        .refine((values) => values.newPassword === values.repeatPassword, {
            message: t("repeatPasswordError"),
            path: ["repeatPassword"],
        }),
);
const form = useForm({
    validationSchema: formSchema,
});

const error = ref<string | null>(null);
const showDialog = ref(false);

const { isPending, mutate } = useMutation({
    mutationKey: ["changePassword"],
    mutationFn: async ({ currentPassword, newPassword }: { currentPassword: string; newPassword: string }) => {
        await UserApiService.setNewPassword(currentPassword, newPassword);
    },
    onSuccess: () => {
        showDialog.value = false;
    },
    onError: (reason) => {
        error.value = reason instanceof ApiError ? reason.getTranslationId() : "unknownError";
    },
});

const onSubmit = form.handleSubmit(async (values) => mutate(values));
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger as-child>
            <Button size="sm" variant="secondary">{{ t("change") }}</Button>
        </DialogTrigger>
        <DialogContent>
            <form @submit="onSubmit" class="space-y-3">
                <DialogHeader>
                    <DialogTitle>{{ t("changePassword") }}</DialogTitle>
                    <DialogDescription>{{ t("changePasswordDescription") }}</DialogDescription>
                </DialogHeader>
                <FormField v-slot="{ componentField }" name="currentPassword">
                    <FormItem>
                        <FormLabel>{{ t("currentPassword") }}</FormLabel>
                        <FormControl>
                            <PasswordInput v-bind="componentField" :disabled="isPending" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="newPassword">
                    <FormItem>
                        <FormLabel>{{ t("newPassword") }}</FormLabel>
                        <FormControl>
                            <PasswordInput v-bind="componentField" :disabled="isPending" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="repeatPassword">
                    <FormItem>
                        <FormLabel>{{ t("repeatPassword") }}</FormLabel>
                        <FormControl>
                            <PasswordInput v-bind="componentField" :disabled="isPending" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <ErrorBanner v-if="error" :description="t(error)" />
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline" type="button">Anuluj</Button>
                    </DialogClose>
                    <Button type="submit">
                        <template v-if="!isPending">{{ t("confirm") }}</template>
                        <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
