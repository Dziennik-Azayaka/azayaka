<script setup lang="ts">
import GradebookSelector from './components/layout/GradebookSelector.vue';
import { getMenuItems } from './navigation';
import PanelNavigation from '@/components/panel-layout/PanelNavigation.vue';
import PanelNavigationHeaderMenu from '@/components/panel-layout/PanelNavigationHeaderMenu.vue';
import PanelNavigationItem from '@/components/panel-layout/PanelNavigationItem.vue';
import PanelLayout from '@/layouts/PanelLayout.vue';
import { useGradebookStore } from '@/stores/gradebook';
import { usePreferencesStore } from '@/stores/preferences';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const userStore = useUserStore();
const preferencesStore = usePreferencesStore();
const gradebookStore = useGradebookStore();
</script>

<template>
  <PanelLayout v-if="userStore.user">
    <template #navigation>
      <PanelNavigation>
        <template #header>
          <PanelNavigationHeaderMenu
            :title="t('gradebook.title')"
            :subtitle="userStore.access?.name"
          />
        </template>
        <template #navigation-top>
          <GradebookSelector />
          <template v-if="gradebookStore.selectedGradebook">
            <PanelNavigationItem
              v-for="({ title, icon, link }, index) in getMenuItems()"
              :key="index"
              :title="t(title)"
              :icon="icon"
              :link="link"
              @click="preferencesStore.mobileNavOpen = false"
            />
          </template>
        </template>
      </PanelNavigation>
    </template>
  </PanelLayout>
</template>
