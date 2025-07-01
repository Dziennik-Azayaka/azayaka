<script setup lang="ts">
import { LucideLoader2 } from "lucide-vue-next";
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

import { Button, Input } from "@azayaka-frontend/ui";

import ErrorBanner from "@/components/ErrorBanner.vue";
import FormHeader from "@/components/FormHeader.vue";
import { useActivationStore } from "@/stores/activation.store";

const { t } = useI18n();
const router = useRouter();
const activationStore = useActivationStore();
const isLoading = ref(false);
const error = ref<string | null>(null);
const code = ref(activationStore.code ? activationStore.code : new Array(10).fill(""));
const onSubmit = () => {
    isLoading.value = true;
    if (activationStore.code === code.value)
        return router.push({
            name: "activation.emailAddress",
        });
    error.value = null;

    // FAKE
    setTimeout(() => {
        if (code.value.toString() !== ["krzak", "test", "", "", "", "", "", "", "", ""].toString()) {
            error.value = "incorrectCodeError";
            isLoading.value = false;
        } else {
            activationStore.code = code.value;
            router.push({
                name: "activation.emailAddress",
            });
        }
    }, 2500);
};
</script>

<template>
    <div>
        <FormHeader :title="t('accessActivation')" :subtitle="t('activationCodeDescription')" />
        <form @submit.prevent="onSubmit()" class="space-y-6 mt-5">
            <ol class="list-decimal grid grid-cols-2 gap-y-4 gap-x-6 marker:leading-9">
                <li class="pl-2 ml-6" v-for="i in 10" :key="i">
                    <Input type="text" :disabled="isLoading" v-model="code[i - 1]" autocapitalize="off" />
                </li>
            </ol>
            <p class="text-end">
                <button
                    type="button"
                    v-if="router.options.history.state['back'] === '/log-in'"
                    @click="router.back()"
                    class="hover:underline inline font-medium text-sm cursor-pointer text-primary"
                    :disabled="isLoading"
                >
                    {{ t("back") }}
                </button>
            </p>

            <ErrorBanner :description="t(error)" v-if="error" />
            <Button type="submit" class="w-full" :disabled="isLoading">
                <template v-if="!isLoading">{{ t("next") }}</template>
                <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
            </Button>
        </form>
    </div>
</template>
