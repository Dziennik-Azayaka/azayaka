import { defineStore } from "pinia";
import { ref } from "vue";

export const useActivationStore = defineStore("activation", () => {
    const code = ref<string[] | null>(null);
    const emailAddress = ref<string | null>(null);
    const accountExist = ref<boolean | null>(null);

    return { code, emailAddress, accountExist };
});
