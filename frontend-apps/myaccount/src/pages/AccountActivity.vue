<script setup lang="ts">
import { Button } from "#ui/components/ui/button";
import { LucideAlertCircle, LucideLoaderCircle, RefreshCw } from "lucide-vue-next";
import { onMounted, ref, watch } from "vue";
import { useI18n } from "vue-i18n";

import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from "@azayaka-frontend/ui";

import type { ActivityLogEntryEntity } from "@/api/entities/activity-log-entry.ts";
import UserApiService from "@/api/services/user.ts";
import PanelHeader from "@/components/PanelHeader.vue";

const { t, d } = useI18n();

const activities = ref<ActivityLogEntryEntity[] | null>(null);
const loading = ref(true);
const error = ref(false);

const paginationInfo = ref({
    page: 1,
    perPage: 50,
    total: 0,
});
const paginationModel = ref(1);

watch(paginationModel, getPage);

async function getPage() {
    loading.value = true;
    error.value = false;
    try {
        const result = await UserApiService.getActivityLog(paginationModel.value);
        paginationInfo.value = result.pagination;
        activities.value = result.data;
    } catch {
        error.value = true;
    } finally {
        loading.value = false;
    }
}

const activityNameTranslationId = (type: ActivityLogEntryEntity["type"]) => {
    switch (type) {
        case "failed_login_attempt":
            return "activity.failedLoginAttempt";
        case "logged_out_by_another_device":
            return "activity.loggedOutByAnotherDevice";
        case "logout":
            return "activity.loggedOut";
        case "credentials_changed":
            return "activity.credentialsChanged";
        case "successful_login_attempt":
            return "activity.loggedIn";
    }
};

onMounted(getPage);
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: '/', title: t('activityHistory') }]" />
        <main id="main-content">
            <h1 class="text-2xl font-semibold">{{ t("activityHistory") }}</h1>
            <p class="text-foreground/70 mb-4 text-sm">
                {{ t("activityHistoryDescription") }}
            </p>
            <div class="h-96 content-center" v-if="loading">
                <LucideLoaderCircle class="animate-spin mx-auto" :aria-label="t('pleaseWait')" />
            </div>
            <div class="h-96 flex flex-col items-center justify-center" v-else-if="error">
                <LucideAlertCircle class="mx-auto size-9" />
                <p class="text-center mt-2 font-medium">{{ t("unknownError") }}</p>
                <Button class="mx-auto mt-3" variant="outline" @click="getPage">
                    <RefreshCw />
                    {{ t("tryAgain") }}
                </Button>
            </div>
            <template v-else>
                <div class="rounded-md overflow-hidden border mb-4 not-md:hidden shadow-xs">
                    <table class="w-full">
                        <thead class="bg-accent">
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium">{{ t("activity") }}</td>
                                <td class="px-4 py-3 text-sm font-medium">{{ t("date") }}</td>
                                <td class="px-4 py-3 text-sm font-medium">{{ t("device") }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t" v-for="(activity, index) in activities" :key="index">
                                <td class="px-4 py-3 text-sm">{{ t(activityNameTranslationId(activity.type)) }}</td>
                                <td class="px-4 py-3 text-sm text-foreground/70">{{ d(activity.date, "long") }}</td>
                                <td class="px-4 py-3 text-sm text-foreground/70">
                                    <template
                                        v-if="activity.device.os || activity.device.name || activity.device.software"
                                    >
                                        <template v-if="activity.device.name"> {{ activity.device.name }}, </template>
                                        {{ activity.device.os }}
                                        <template v-if="activity.device.software">
                                            ({{ activity.device.software }}),
                                        </template>
                                    </template>
                                    <template v-else>{{ t("unknownDevice") }}</template>
                                    {{ t("ipAddress") }}:
                                    {{ activity.device.ipAddress }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <ul class="space-y-3 mb-4 md:hidden">
                    <li
                        class="border rounded-md shadow-xs overflow-hidden px-4 py-3"
                        v-for="(activity, index) in activities"
                        :key="index"
                    >
                        <h2 class="font-semibold">{{ t(activityNameTranslationId(activity.type)) }}</h2>
                        <p class="text-sm">{{ d(activity.date, "long") }}</p>
                        <p class="mt-1 text-sm text-foreground/70">
                            <template v-if="activity.device.os || activity.device.name || activity.device.software">
                                <template v-if="activity.device.name"> {{ activity.device.name }}, </template>
                                {{ activity.device.os }}
                                <template v-if="activity.device.software"> ({{ activity.device.software }}), </template>
                            </template>
                            <template v-else>{{ t("unknownDevice") }}</template>
                            {{ t("ipAddress") }}:
                            {{ activity.device.ipAddress }}
                        </p>
                    </li>
                </ul>
                <Pagination
                    v-model:page="paginationModel"
                    v-slot="{ page }"
                    :items-per-page="paginationInfo.perPage"
                    :total="paginationInfo.total"
                    :default-page="paginationInfo.page"
                >
                    <PaginationContent v-slot="{ items }">
                        <PaginationPrevious :text="t('previousPage')" />
                        <template v-for="(item, index) in items" :key="index">
                            <PaginationItem
                                v-if="item.type === 'page'"
                                :value="item.value"
                                :is-active="item.value === page"
                            >
                                {{ item.value }}
                            </PaginationItem>
                        </template>
                        <PaginationEllipsis :index="4" />
                        <PaginationNext :text="t('nextPage')" />
                    </PaginationContent>
                </Pagination>
            </template>
        </main>
    </div>
</template>
