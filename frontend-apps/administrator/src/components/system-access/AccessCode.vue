<script setup lang="ts">
import { ref } from "vue";
import { useI18n } from "vue-i18n";

defineProps<{ code: string[] }>();

const { t } = useI18n();
const showCode = ref(false);
</script>

<template>
    <div class="py-4 px-5 bg-accent border rounded-md relative">
        <Transition leave-from-class="opacity-100" leave-to-class="opacity-0">
            <button
                class="absolute top-0 left-0 right-0 bottom-0 bg-accent cursor-pointer text-sm text-center transition-opacity"
                @click="showCode = true"
                v-if="!showCode"
            >
                {{ t("accessClick") }}
            </button>
        </Transition>
        <div>
            <p class="mb-1 font-medium">{{ t("accessActivationCode") }}:</p>
            <ol class="list-decimal grid grid-cols-2 list-inside text-sm">
                <li v-for="(word, index) in code" :key="index">{{ word }}</li>
            </ol>
        </div>
    </div>
</template>
