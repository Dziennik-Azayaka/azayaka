<script setup lang="ts">
import { menuItems } from './navigation';
import PanelNavigation from '@/components/panel-layout/PanelNavigation.vue';
import PanelNavigationHeaderMenu from '@/components/panel-layout/PanelNavigationHeaderMenu.vue';
import PanelNavigationItem from '@/components/panel-layout/PanelNavigationItem.vue';
import PanelLayout from '@/layouts/PanelLayout.vue';
import { usePreferencesStore } from '@/stores/preferences';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const userStore = useUserStore();
const preferencesStore = usePreferencesStore();
</script>

<template>
  <PanelLayout v-if="userStore.user">
    <template #navigation>
      <PanelNavigation>
        <template #header>
          <PanelNavigationHeaderMenu
            :title="t('myAccount.title')"
            :subtitle="userStore.user.email"
          />
        </template>
        <template #navigation-top>
          <PanelNavigationItem
            v-for="({ title, icon, link }, index) in menuItems"
            :key="index"
            :title="t(title)"
            :icon="icon"
            :link="link"
            @click="preferencesStore.mobileNavOpen = false"
          />
        </template>
        <!--<template #navigation-bottom>
          <PanelNavigationItem title="Pomoc" :icon="LucideHelpCircle" link="/help" />
        </template>-->
      </PanelNavigation>
    </template>
  </PanelLayout>
</template>
