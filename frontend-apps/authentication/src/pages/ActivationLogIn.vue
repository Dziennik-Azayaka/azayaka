<script setup lang="ts">
import { toTypedSchema } from "@vee-validate/zod";
import { LucideLoader2 } from "lucide-vue-next";
import { useForm } from "vee-validate";
import { onBeforeMount, ref } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import * as z from "zod";

import { Button, FormControl, FormField, FormItem, FormLabel, FormMessage, PasswordInput } from "@azayaka-frontend/ui";

import ErrorBanner from "@/components/ErrorBanner.vue";
import FormHeader from "@/components/FormHeader.vue";
import { useActivationStore } from "@/stores/activation.store";

const { t } = useI18n();
const router = useRouter();
const activationStore = useActivationStore();

onBeforeMount(() => {
    if (!activationStore.code || !activationStore.emailAddress || !activationStore.accountExist)
        router.replace({
            name: "activation.emailAddress",
        });
});

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
const onSubmit = form.handleSubmit((values) => {
    isLoading.value = true;
    error.value = null;

    // FAKE
    setTimeout(() => {
        if (values.password !== "jan123") error.value = "incorrectPasswordError";
        isLoading.value = false;
    }, 2500);
});

function back() {
    if (router.options.history.state["back"] === "/access-activation/email-address") router.back();
    else router.push({ name: "activation.code" });
}
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
