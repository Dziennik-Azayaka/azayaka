<script setup lang="ts">
import ErrorBanner from "#ui/components/ui/banner/ErrorBanner.vue";
import SuccessBanner from "#ui/components/ui/banner/SuccessBanner.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { LucideLoader2 } from "lucide-vue-next";
import { useForm } from "vee-validate";
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import * as z from "zod";

import { Button, FormControl, FormField, FormItem, FormLabel, FormMessage, Input } from "@azayaka-frontend/ui";

import FormHeader from "@/components/FormHeader.vue";

const { t } = useI18n();
const router = useRouter();

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

const isLoading = ref(false);
const error = ref<string | null>(null);
const messageSent = ref<false | { emailAddress: string }>(false);

const onSubmit = form.handleSubmit((values) => {
    console.log("Form submitted!", values);
    isLoading.value = true;
    error.value = null;
    messageSent.value = false;

    // FAKE
    setTimeout(() => {
        if (values.emailAddress === "jan@fakelog.cf") messageSent.value = { emailAddress: values.emailAddress };
        else error.value = "incorrectEmailAddressError";
        isLoading.value = false;
    }, 2500);
});

function back() {
    if (router.options.history.state["back"] === "/log-in") router.back();
    else router.push({ name: "logIn" });
}
</script>

<template>
    <div>
        <FormHeader :title="t('forgotPassword')" :subtitle="t('forgotPasswordFormDescription')" />
        <form @submit.prevent="onSubmit()" class="space-y-6 mt-5">
            <FormField v-slot="{ componentField }" name="emailAddress">
                <FormItem>
                    <FormLabel>{{ t("emailAddress") }}</FormLabel>
                    <FormControl>
                        <Input type="text" v-bind="componentField" :disabled="isLoading" />
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
            <SuccessBanner :description="t('messageSent', messageSent)" v-if="messageSent" />
            <Button type="submit" class="w-full" :disabled="isLoading">
                <template v-if="!isLoading">{{ t("confirm") }}</template>
                <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
            </Button>
        </form>
    </div>
</template>
