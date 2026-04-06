<script setup lang="ts">
import LogoDark from '../../../static/logo-dark.svg';
import LogoLight from '../../../static/logo-light.svg';
import { Breadcrumb, BreadcrumbItem, BreadcrumbList, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { Button } from '@/components/ui/button';
import PanelAccountMenu from './PanelAccountMenu.vue';
import { usePreferencesStore } from '@/stores/preferences';
import { LucideMenu } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  type RouteLocationAsPathGeneric,
  type RouteLocationAsRelativeGeneric,
  RouterLink,
  useRoute,
} from 'vue-router';

const route = useRoute();
const preferencesStore = usePreferencesStore();

interface BreadcrumbItem {
  name: string;
  route?: string | RouteLocationAsRelativeGeneric | RouteLocationAsPathGeneric;
}

const { t } = useI18n();
const breadcrumb = computed(() => route.meta.breadcrumb as BreadcrumbItem[] | undefined);

function toggleNavigation() {
  preferencesStore.mobileNavOpen = !preferencesStore.mobileNavOpen;
}
</script>

<template>
  <header class="items-center flex justify-between">
    <Button size="icon" variant="outline" class="xl:hidden" @click="toggleNavigation">
      <LucideMenu />
    </Button>
    <Breadcrumb class="not-xl:hidden" v-if="breadcrumb">
      <BreadcrumbList>
        <BreadcrumbItem>
          <img
            class="h-6 logo"
            :src="preferencesStore.colorMode === 'dark' ? LogoDark : LogoLight"
            alt="Logo"
          />
        </BreadcrumbItem>
        <template v-for="(item, index) in breadcrumb" :key="index">
          <BreadcrumbSeparator />
          <BreadcrumbItem
            class="text-muted-foreground"
            :class="{ 'text-primary! font-medium': index === breadcrumb.length - 1 }"
          >
            <component :is="item.route ? RouterLink : 'span'" :to="item.route">
              {{ t(item.name) }}
            </component>
          </BreadcrumbItem>
        </template>
      </BreadcrumbList>
    </Breadcrumb>

    <PanelAccountMenu />
  </header>
</template>
