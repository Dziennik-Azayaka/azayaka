<script setup lang="ts">
import { LucideLoader2 } from "lucide-vue-next";
import { onBeforeMount, ref } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

import { Button } from "@azayaka-frontend/ui";

import FormHeader from "@/components/FormHeader.vue";

const { t } = useI18n();
const router = useRouter();

onBeforeMount(() => {
    // TODO
});

const isLoading = ref(false);

function back() {
    if (router.options.history.state["back"] === "/access-activation/set-password") router.back();
    else router.push({ name: "activation.setPassword" });
}
</script>

<template>
    <div>
        <FormHeader
            :title="t('accessActivation')"
            :subtitle="t('activationEmailAddressConfirmationDescription', { emailAddress: 'jan@fakelog.cf' })"
        />
        <p class="text-right">
            <button
                @click="back()"
                class="text-sm font-medium text-primary hover:underline cursor-pointer"
                type="button"
            >
                {{ t("back") }}
            </button>
        </p>
        <p class="text-sm my-2 text-foreground/80">
            {{ t("sendMessageAgainHint") }}
        </p>
        <Button type="submit" class="w-full" :disabled="isLoading" variant="secondary">
            <template v-if="!isLoading">{{ t("sendMessageAgain") }}</template>
            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
        </Button>
    </div>
</template>
