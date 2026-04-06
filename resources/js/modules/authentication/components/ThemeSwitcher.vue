<script setup lang="ts">
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuLabel,
  DropdownMenuRadioGroup,
  DropdownMenuRadioItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { usePreferencesStore } from '@/stores/preferences';
import { LucideContrast } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const preferencesStore = usePreferencesStore();
const { t } = useI18n();
</script>

<template>
  <DropdownMenu aria-hidden="true">
    <DropdownMenuTrigger :title="t('preferences.theme')" class="cursor-pointer">
      <LucideContrast />
    </DropdownMenuTrigger>
    <DropdownMenuContent>
      <DropdownMenuLabel>{{ t('preferences.theme') }}</DropdownMenuLabel>
      <DropdownMenuRadioGroup v-model="preferencesStore.colorMode">
        <DropdownMenuRadioItem value="light">
          {{ t('preferences.themes.light') }}
        </DropdownMenuRadioItem>
        <DropdownMenuRadioItem value="dark">
          {{ t('preferences.themes.dark') }}
        </DropdownMenuRadioItem>
        <DropdownMenuRadioItem value="high-contrast">
          {{ t('preferences.themes.highContrast') }}
        </DropdownMenuRadioItem>
      </DropdownMenuRadioGroup>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
