<script setup lang="ts">
import { Input } from ".";
import { LucideEye, LucideEyeOff } from "lucide-vue-next";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const showPassword = ref(false);
function togglePasswordVisiblity() {
    showPassword.value = !showPassword.value;
}
</script>

<template>
    <div class="relative">
        <Input :type="showPassword ? 'text' : 'password'" v-bind="$attrs" autocapitalize="off" class="pr-8" />
        <button
            class="right-1 absolute size-7 top-1 bottom-1 hover:bg-accent rounded-md content-center cursor-pointer transition-colors"
            type="button"
            :title="showPassword ? t('hidePassword') : t('showPassword')"
            @click="togglePasswordVisiblity()"
        >
            <LucideEye class="size-4 mx-auto" aria-hidden="true" v-if="!showPassword" />
            <LucideEyeOff class="size-4 mx-auto" aria-hidden="true" v-else />
        </button>
    </div>
</template>
