<script setup lang="ts">
import ErrorBanner from "#ui/components/ui/banner/ErrorBanner.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { LucideLoader2 } from "lucide-vue-next";
import { useForm } from "vee-validate";
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import * as z from "zod";

import { Button, FormControl, FormField, FormItem, FormLabel, FormMessage, Input } from "@azayaka-frontend/ui";

import ActivationApiService from "@/api/services/activation";
import FormHeader from "@/components/FormHeader.vue";
import { useActivationStore } from "@/stores/activation.store";
import { backOrPush } from "@/utils/back-or-push";

const { t } = useI18n();
const router = useRouter();
const activationStore = useActivationStore();

const formSchema = toTypedSchema(
    z.object({
        emailAddress: z
            .string({
                required_error: t("requiredEmailAddressError"),
            })
            .email(t("invalidEmailAddressError")),
    }),
);

const form = useForm({
    validationSchema: formSchema,
    initialValues: { emailAddress: "email" in activationStore.status ? activationStore.status.email : undefined },
});

const isLoading = ref(false);
const error = ref<string | null>(null);
const onSubmit = form.handleSubmit(async (values) => {
    isLoading.value = true;
    error.value = null;

    if (activationStore.status.step === "notStarted") return;

    if ("email" in activationStore.status && activationStore.status.email === values.emailAddress)
        return await router.push({
            name: activationStore.status.step === "attach_to_account" ? "activation.logIn" : "activation.setPassword",
        });

    try {
        const { accountExist } = await ActivationApiService.checkEmailAddress(values.emailAddress);
        activationStore.status = {
            step: accountExist ? "attach_to_account" : "email_available",
            code: activationStore.status.code,
            email: values.emailAddress,
        };
        await router.push({
            name: accountExist ? "activation.logIn" : "activation.setPassword",
        });
    } catch (err: unknown) {
        error.value = (err as Error).message;
    } finally {
        isLoading.value = false;
    }
});

const back = () => backOrPush(router, "activation.code");
</script>

<template>
    <div>
        <FormHeader :title="t('accessActivation')" :subtitle="t('activationEmailAddressDescription')" />
        <form @submit.prevent="onSubmit()" class="space-y-6 mt-5">
            <FormField v-slot="{ componentField }" name="emailAddress">
                <FormItem>
                    <FormLabel>{{ t("emailAddress") }}</FormLabel>
                    <FormControl>
                        <Input type="text" v-bind="componentField" :disabled="isLoading" autocapitalize="off" />
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
