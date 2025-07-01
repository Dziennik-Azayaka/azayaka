<script setup lang="ts">
import GbSquare from "./Flags/GbSquare.vue";
import PlSquare from "./Flags/PlSquare.vue";
import { useI18n } from "vue-i18n";

const { locale, t } = useI18n();

const languages = {
    pl: { name: "Polski", flag: PlSquare },
    en: { name: "English", flag: GbSquare },
};
</script>

<template>
    <fieldset class="flex items-end gap-3 rounded-md">
        <legend class="sr-only">
            {{ t("appLanguage") }}
        </legend>
        <label v-for="({ name, flag }, code) in languages" :key="code">
            <component
                :is="flag"
                class="!size-8 rounded-full block cursor-pointer transition-all border"
                :class="{ 'ring-2 ring-primary ring-offset-2': locale.startsWith(code) }"
                :aria-label="name"
                :title="name"
            />
            <input type="radio" name="language" :value="code" v-model="locale" class="sr-only" />
        </label>
    </fieldset>
</template>
