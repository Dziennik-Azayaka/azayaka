<script setup lang="ts">
import ErrorBanner from "#ui/components/ui/banner/ErrorBanner.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { LucideLoader2 } from "lucide-vue-next";
import { useForm } from "vee-validate";
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import * as z from "zod";

import { Button, FormControl, FormField, FormItem, FormLabel, FormMessage, PasswordInput } from "@azayaka-frontend/ui";

import FormHeader from "@/components/FormHeader.vue";

const { t } = useI18n();

const formSchema = toTypedSchema(
    z
        .object({
            password: z
                .string({
                    required_error: t("requiredPasswordError"),
                })
                .min(8, t("passwordMinLengthError"))
                .max(255, t("passwordMaxLengthError")),
            repeatPassword: z.string({
                required_error: t("requiredPasswordError"),
            }),
        })
        .refine((values) => values.password === values.repeatPassword, {
            message: t("repeatPasswordError"),
            path: ["repeatPassword"],
        }),
);
const form = useForm({
    validationSchema: formSchema,
});

const error = ref<string | null>(null);
const isLoading = ref(false);
const onSubmit = form.handleSubmit(() => {
    isLoading.value = true;
    error.value = null;

    // FAKE
    setTimeout(() => {
        isLoading.value = false;
    }, 2500);
});
</script>

<template>
    <div>
        <FormHeader :title="t('forgotPassword')" :subtitle="t('forgotPasswordSetNewDescription')" />
        <form @submit.prevent="onSubmit()" class="space-y-6 mt-5">
            <FormField v-slot="{ componentField }" name="password">
                <FormItem>
                    <FormLabel>{{ t("password") }}</FormLabel>
                    <FormControl>
                        <PasswordInput v-bind="componentField" :disabled="isLoading" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="repeatPassword">
                <FormItem>
                    <FormLabel>{{ t("repeatPassword") }}</FormLabel>
                    <FormControl>
                        <PasswordInput v-bind="componentField" :disabled="isLoading" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <ErrorBanner :description="t(error)" v-if="error" />
            <Button type="submit" class="w-full" :disabled="isLoading">
                <template v-if="!isLoading">{{ t("confirm") }}</template>
                <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
            </Button>
        </form>
    </div>
</template>
