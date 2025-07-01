<script setup lang="ts">
import { toTypedSchema } from "@vee-validate/zod";
import { LucideLoader2 } from "lucide-vue-next";
import { useForm } from "vee-validate";
import { onBeforeMount, ref } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import * as z from "zod";

import { Button, FormControl, FormField, FormItem, FormLabel, FormMessage, Input } from "@azayaka-frontend/ui";

import ErrorBanner from "@/components/ErrorBanner.vue";
import FormHeader from "@/components/FormHeader.vue";
import { useActivationStore } from "@/stores/activation.store";

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
});

onBeforeMount(() => {
    if (!activationStore.code)
        router.replace({
            name: "activation.code",
        });
    if (activationStore.emailAddress) form.setFieldValue("emailAddress", activationStore.emailAddress);
});

const isLoading = ref(false);
const error = ref<string | null>(null);
const onSubmit = form.handleSubmit((values) => {
    isLoading.value = true;
    if (activationStore.emailAddress === values.emailAddress && activationStore.accountExist)
        return router.push({
            name: activationStore.accountExist ? "activation.logIn" : "activation.setPassword",
        });
    error.value = null;

    // FAKE
    setTimeout(() => {
        activationStore.emailAddress = values.emailAddress;
        activationStore.accountExist = values.emailAddress === "jan@fakelog.cf";
        router.push({
            name: activationStore.accountExist ? "activation.logIn" : "activation.setPassword",
        });
    }, 2500);
});

function back() {
    if (router.options.history.state["back"] === "/access-activation/code") router.back();
    else router.push({ name: "activation.code" });
}
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
