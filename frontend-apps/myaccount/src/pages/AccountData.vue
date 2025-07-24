<script setup lang="ts">
import { useI18n } from "vue-i18n";

import ChangeEmailDialog from "@/components/ChangeEmailDialog.vue";
import ChangePasswordDialog from "@/components/ChangePasswordDialog.vue";
import PanelHeader from "@/components/PanelHeader.vue";
import { useMainStore } from "@/stores/main.store.ts";

const { t } = useI18n();
const mainStore = useMainStore();
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'data' }, title: t('accountData') }]" />
        <main id="main-content">
            <h1 class="text-2xl font-semibold">{{ t("accountData") }}</h1>
            <p class="text-foreground/70 mb-4 text-sm">
                {{ t("accountDataDescription") }}
            </p>
            <div class="rounded-md overflow-hidden border mb-4 shadow-xs">
                <dl class="grid md:grid-cols-[fit-content(100%)_1fr]">
                    <div class="contents not-first:*:first:border-t md:not-first:*:border-t">
                        <dt class="md:h-13 not-md:pt-4 content-center px-4 text-sm font-medium">
                            {{ t("emailAddress") }}
                        </dt>
                        <dd class="md:h-13 not-md:pb-4 px-4 flex justify-between items-center">
                            <span class="text-sm text-foreground/80">{{ mainStore.emailAddress }}</span>
                            <ChangeEmailDialog />
                        </dd>
                    </div>
                    <div class="contents not-first:*:first:border-t md:not-first:*:border-t">
                        <dt class="md:h-13 not-md:pt-4 content-center px-4 text-sm font-medium">{{ t("password") }}</dt>
                        <dd class="md:h-13 not-md:pb-4 px-4 flex justify-between items-center">
                            <span class="text-sm text-foreground/80">zmieniono 3 dni temu</span>
                            <ChangePasswordDialog />
                        </dd>
                    </div>
                </dl>
            </div>
        </main>
    </div>
</template>
