<script setup lang="ts">
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from "@azayaka-frontend/ui";

import PanelHeader from "@/components/PanelHeader.vue";

const { t, d } = useI18n();

const activities = ref([
    {
        type: "loggedIn",
        date: new Date(2025, 5, 13, 14, 55),
        device: {
            name: "Apple iPhone",
            os: "iOS 14.6",
            software: "Safari 14",
            ip: "192.168.1.101",
        },
    },
    {
        type: "loggedIn",
        date: new Date(2025, 5, 12, 13, 23),
        device: {
            name: null,
            os: "Linux",
            software: "Firefox 139",
            ip: "192.168.1.103",
        },
    },
]);
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: '/', title: t('activityHistory') }]" />
        <main id="main-content">
            <h1 class="text-2xl font-semibold">{{ t("activityHistory") }}</h1>
            <p class="text-foreground/70 mb-4 text-sm">
                {{ t("activityHistoryDescription") }}
            </p>
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
                            <td class="px-4 py-3 text-sm">{{ t(`activity.${activity.type}`) }}</td>
                            <td class="px-4 py-3 text-sm text-foreground/70">{{ d(activity.date, "long") }}</td>
                            <td class="px-4 py-3 text-sm text-foreground/70">
                                <template v-if="activity.device.name"> {{ activity.device.name }}, </template>
                                {{ activity.device.os }} ({{ activity.device.software }}), {{ t("ipAddress") }}:
                                {{ activity.device.ip }}
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
                    <h2 class="font-semibold">{{ t(`activity.${activity.type}`) }}</h2>
                    <p class="text-sm">{{ d(activity.date, "long") }}</p>
                    <p class="mt-1 text-sm text-foreground/70">
                        <template v-if="activity.device.name"> {{ activity.device.name }}, </template>
                        {{ activity.device.os }} ({{ activity.device.software }}), {{ t("ipAddress") }}:
                        {{ activity.device.ip }}
                    </p>
                </li>
            </ul>
            <Pagination v-slot="{ page }" :items-per-page="10" :total="50" :default-page="1">
                <PaginationContent v-slot="{ items }">
                    <PaginationPrevious text="Poprzednia strona" />
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
                    <PaginationNext text="Następna strona" />
                </PaginationContent>
            </Pagination>
        </main>
    </div>
</template>
