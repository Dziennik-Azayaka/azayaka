<script setup lang="ts">
import AccountDataEmailChange from '@/modules/my-account/components/AccountDataEmailChange.vue';
import AccountDataPasswordChange from '@/modules/my-account/components/AccountDataPasswordChange.vue';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { useUserStore } from '@/stores/user';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const userStore = useUserStore();
</script>

<template>
  <PanelPageHeader :title="t('myAccount.accountData.title')" />

  <div class="rounded-md overflow-hidden border mb-4 shadow-xs">
    <dl class="grid md:grid-cols-[fit-content(100%)_1fr]">
      <div class="contents not-first:*:first:border-t md:not-first:*:border-t">
        <dt class="md:h-13 not-md:pt-4 content-center px-4 text-sm font-medium">
          {{ t('common.data.email') }}
        </dt>
        <dd class="md:h-13 not-md:pb-4 px-4 flex justify-between items-center">
          <span class="text-sm text-foreground/80">{{ userStore.user?.email }}</span>
          <AccountDataEmailChange />
        </dd>
      </div>
      <div class="contents not-first:*:first:border-t md:not-first:*:border-t">
        <dt class="md:h-13 not-md:pt-4 content-center px-4 text-sm font-medium">
          {{ t('common.data.password') }}
        </dt>
        <dd class="md:h-13 not-md:pb-4 px-4 flex justify-between items-center">
          <span class="text-sm text-foreground/80">*******</span>
          <AccountDataPasswordChange />
        </dd>
      </div>
    </dl>
  </div>
</template>
