<script setup lang="ts">
import ErrorBanner from "#ui/components/ui/banner/ErrorBanner.vue";
import PasswordInput from "#ui/components/ui/input/PasswordInput.vue";
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
    Input,
} from "@azayaka-frontend/ui";

import { ApiError } from "@/api/error";
import UserApiService from "@/api/services/user";
import { useUserStore } from "@/stores/user.store";

const { t } = useI18n();
const userStore = useUserStore();
const showDialog = ref(false);

const formSchema = toTypedSchema(
    z.object({
        emailAddress: z
            .string({
                required_error: t("requiredEmailAddressError"),
            })
            .email(t("invalidEmailAddressError")),
        password: z.string({
            required_error: t("requiredPasswordError"),
        }),
    }),
);
const form = useForm({
    validationSchema: formSchema,
});

const error = ref<string | null>();

const { isPending, mutate } = useMutation({
    mutationKey: ["changeEmailAddress"],
    mutationFn: async ({ emailAddress, password }: { emailAddress: string; password: string }) => {
        await UserApiService.setNewEmailAddress(emailAddress, password);
        userStore.user!.emailAddress = emailAddress;
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
                    <DialogTitle>{{ t("changeEmailAddress") }}</DialogTitle>
                    <DialogDescription>{{ t("changeEmailAddressDescription") }}</DialogDescription>
                </DialogHeader>
                <FormField v-slot="{ componentField }" name="emailAddress">
                    <FormItem>
                        <FormLabel>{{ t("newEmailAddress") }}</FormLabel>
                        <FormControl>
                            <Input type="text" v-bind="componentField" :disabled="isPending" autocapitalize="off" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="password">
                    <FormItem>
                        <FormLabel>{{ t("currentPassword") }}</FormLabel>
                        <FormControl>
                            <PasswordInput v-bind="componentField" :disabled="isPending" autocapitalize="off" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <ErrorBanner v-if="error" :description="t(error)" />
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline" type="button">{{ t("cancel") }}</Button>
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
