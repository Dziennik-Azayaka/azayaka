import { defineStore } from "pinia";
import { ref } from "vue";

import type { ActivationStatus } from "@/api/dto/activation-status";
import ActivationApiService from "@/api/services/activation";

export const useActivationStore = defineStore("activation", () => {
    const needSync = ref(true);
    const status = ref<ActivationStatus>({ step: "notStarted" });

    async function syncWithApi() {
        status.value = await ActivationApiService.getStatus();
    }

    return { status, needSync, syncWithApi };
});
