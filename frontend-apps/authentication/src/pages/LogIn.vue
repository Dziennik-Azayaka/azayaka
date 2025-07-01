<script setup lang="ts">
import { toTypedSchema } from "@vee-validate/zod";
import { LucideLoader2 } from "lucide-vue-next";
import { useForm } from "vee-validate";
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import * as z from "zod";

import {
    Button,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
    Input,
    PasswordInput,
} from "@azayaka-frontend/ui";

import ActivationBanner from "@/components/ActivationBanner.vue";
import ErrorBanner from "@/components/ErrorBanner.vue";
import FormHeader from "@/components/FormHeader.vue";

const { t } = useI18n();

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

const error = ref<string | null>(null);
const isLoading = ref(false);

const onSubmit = form.handleSubmit((values) => {
    isLoading.value = true;
    error.value = null;

    // FAKE
    setTimeout(() => {
        if (values.emailAddress !== "jan@fakelog.cf") error.value = "incorrectCredentialsError";
        isLoading.value = false;
    }, 2500);
});
</script>

<template>
    <div>
        <FormHeader :title="t('logIn')" :subtitle="t('logInFormDescription')" />

        <form @submit.prevent="onSubmit()" class="space-y-6 mt-5">
            <FormField v-slot="{ componentField }" name="emailAddress">
                <FormItem>
                    <FormLabel>{{ t("emailAddress") }}</FormLabel>
                    <FormControl>
                        <Input type="text" v-bind="componentField" :disabled="isLoading" autocapitalize="off" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="password">
                <FormItem>
                    <div class="flex justify-between">
                        <FormLabel>{{ t("password") }}</FormLabel>
                        <RouterLink
                            :to="{
                                name: 'resetPassword',
                            }"
                            class="text-sm font-medium text-primary hover:underline"
                        >
                            {{ t("forgotPassword") }}
                        </RouterLink>
                    </div>
                    <FormControl>
                        <PasswordInput v-bind="componentField" :disabled="isLoading" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <ErrorBanner :description="t(error)" v-if="error" />
            <Button type="submit" class="w-full" :disabled="isLoading">
                <template v-if="!isLoading">{{ t("logIn") }}</template>
                <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
            </Button>
        </form>
        <ActivationBanner class="mt-10" />
    </div>
</template>
