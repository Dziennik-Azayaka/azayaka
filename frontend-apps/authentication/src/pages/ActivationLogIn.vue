<script setup lang="ts">
import { toTypedSchema } from "@vee-validate/zod";
import { LucideLoader2 } from "lucide-vue-next";
import { useForm } from "vee-validate";
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import * as z from "zod";

import { Button, FormControl, FormField, FormItem, FormLabel, FormMessage, PasswordInput } from "@azayaka-frontend/ui";

import { IncorrectActivationCodeError, IncorrectCredentialsError } from "@/api/errors";
import ActivationApiService from "@/api/services/activation";
import ErrorBanner from "@/components/ErrorBanner.vue";
import FormHeader from "@/components/FormHeader.vue";
import { useActivationStore } from "@/stores/activation.store";
import { backOrPush } from "@/utils/back-or-push";

const { t } = useI18n();
const router = useRouter();
const activationStore = useActivationStore();

const formSchema = toTypedSchema(
    z.object({
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
const onSubmit = form.handleSubmit(async (values) => {
    isLoading.value = true;
    error.value = null;

    if (activationStore.status.step !== "email_available") return;

    try {
        await ActivationApiService.activate(
            activationStore.status.code.split(","),
            activationStore.status.email,
            values.password,
        );
    } catch (reason) {
        if (reason instanceof IncorrectActivationCodeError) error.value = "incorrectCodeError";
        else if (reason instanceof IncorrectCredentialsError) error.value = "incorrectCredentialsError";
        else error.value = "unknownError";
    } finally {
        isLoading.value = false;
    }
});

const back = () => backOrPush(router, "activation.emailAddress");
</script>

<template>
    <div>
        <FormHeader :title="t('accessActivation')" :subtitle="t('activationExistingUserPasswordDescription')" />
        <form @submit.prevent="onSubmit()" class="space-y-6 mt-5">
            <FormField v-slot="{ componentField }" name="password">
                <FormItem>
                    <FormLabel>{{ t("password") }}</FormLabel>
                    <FormControl>
                        <PasswordInput v-bind="componentField" :disabled="isLoading" />
                    </FormControl>
                    <FormMessage />
                    <p class="text-right">
                        <button
                            @click="back()"
                            class="text-sm font-medium text-primary hover:underline cursor-pointer"
                            type="button"
                        >
                            {{ t("back") }}
                        </button>
                    </p>
                </FormItem>
            </FormField>
            <ErrorBanner :description="t(error)" v-if="error" />
            <Button type="submit" class="w-full" :disabled="isLoading">
                <template v-if="!isLoading">{{ t("next") }}</template>
                <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
            </Button>
        </form>
    </div>
</template>
