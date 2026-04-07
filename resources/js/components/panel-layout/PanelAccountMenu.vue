<script setup lang="ts">
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuPortal,
  DropdownMenuRadioGroup,
  DropdownMenuRadioItem,
  DropdownMenuSeparator,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from '../ui/dropdown-menu';
import PanelAccountMenuTrigger from './PanelAccountMenuTrigger.vue';
import { usePreferencesStore } from '@/stores/preferences';
import { useUserStore } from '@/stores/user';
import {
  LucideALargeSmall,
  LucideContrast,
  LucideLanguages,
  LucideLogOut,
  LucideSettings,
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

const preferencesStore = usePreferencesStore();
const userStore = useUserStore();
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <PanelAccountMenuTrigger />
    </DropdownMenuTrigger>
    <DropdownMenuContent>
      <DropdownMenuLabel>{{ userStore.user?.email }}</DropdownMenuLabel>
      <DropdownMenuSeparator />

      <DropdownMenuSub>
        <DropdownMenuSubTrigger>
          <LucideContrast class="size-4 text-muted-foreground mr-2" aria-hidden="true" />
          {{ t('preferences.theme') }}
        </DropdownMenuSubTrigger>
        <DropdownMenuPortal>
          <DropdownMenuSubContent>
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
          </DropdownMenuSubContent>
        </DropdownMenuPortal>
      </DropdownMenuSub>

      <DropdownMenuSub>
        <DropdownMenuSubTrigger>
          <LucideLanguages class="size-4 text-muted-foreground mr-2" aria-hidden="true" />
          {{ t('preferences.appLanguage') }}
        </DropdownMenuSubTrigger>
        <DropdownMenuPortal>
          <DropdownMenuSubContent>
            <DropdownMenuRadioGroup v-model="locale">
              <DropdownMenuRadioItem value="pl">Polski (pl)</DropdownMenuRadioItem>
              <DropdownMenuRadioItem value="en">English (en)</DropdownMenuRadioItem>
            </DropdownMenuRadioGroup>
          </DropdownMenuSubContent>
        </DropdownMenuPortal>
      </DropdownMenuSub>
      <DropdownMenuSub>
        <DropdownMenuSubTrigger>
          <LucideALargeSmall class="size-4 text-muted-foreground mr-2" aria-hidden="true" />
          {{ t('preferences.fontSize') }}
        </DropdownMenuSubTrigger>
        <DropdownMenuPortal>
          <DropdownMenuSubContent>
            <DropdownMenuRadioGroup v-model="preferencesStore.fontSize">
              <DropdownMenuRadioItem value="normal">
                {{ t('preferences.fontSizes.normal') }}
              </DropdownMenuRadioItem>
              <DropdownMenuRadioItem value="large">
                {{ t('preferences.fontSizes.large') }}
              </DropdownMenuRadioItem>
            </DropdownMenuRadioGroup>
          </DropdownMenuSubContent>
        </DropdownMenuPortal>
      </DropdownMenuSub>
      <DropdownMenuItem as-child>
        <RouterLink :to="{ name: 'myAccount' }" target="_blank">
          <LucideSettings aria-hidden="true" />
          {{ t('myAccount.title') }}
        </RouterLink>
      </DropdownMenuItem>
      <DropdownMenuSeparator />
      <DropdownMenuItem variant="destructive" as-child>
        <a href="/api/logout">
          <LucideLogOut />
          {{ t('common.actions.logOut') }}
        </a>
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
