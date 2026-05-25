<script setup lang="ts">
import { useGetGradebookLessons } from '@/api/hooks/gradebook/useGetGradebookLessons';
import type { Lesson } from '@/api/types/lesson';
import { InfoBanner } from '@/components/ui/banner';
import { Empty, EmptyContent, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { EmptyLoading } from '@/components/ui/empty';
import EmptyLoadingError from '@/components/ui/empty/EmptyLoadingError.vue';
import { useGradebookStore } from '@/stores/gradebook';
import { BookOpenCheck } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import LessonDayCard from '../components/LessonDayCard.vue';
import WeekPicker from '../components/WeekPicker.vue';

const { t } = useI18n();
const gradebookStore = useGradebookStore();

const gradebookId = computed(() => gradebookStore.selectedGradebook?.id);

function getMondayOfWeek(date: Date): Date {
  const d = new Date(date);
  const day = d.getDay();
  const diff = d.getDate() - day + (day === 0 ? -6 : 1);
  d.setDate(diff);
  d.setHours(0, 0, 0, 0);
  return d;
}

function formatDate(date: Date): string {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

const currentMonday = ref(getMondayOfWeek(new Date()));

const dateFrom = computed(() => formatDate(currentMonday.value));
const dateTo = computed(() => {
  const sunday = new Date(currentMonday.value);
  sunday.setDate(sunday.getDate() + 6);
  return formatDate(sunday);
});

const {
  data: lessons,
  isFetching,
  isError,
  refetch,
} = useGetGradebookLessons(gradebookId, dateFrom, dateTo);

const lessonsByDay = computed(() => {
  const grouped = new Map<string, Lesson[]>();
  for (const lesson of lessons.value ?? []) {
    const key = formatDate(lesson.date);
    if (!grouped.has(key)) grouped.set(key, []);
    grouped.get(key)!.push(lesson);
  }
  return grouped;
});

const sortedDays = computed(() =>
  [...lessonsByDay.value.entries()].sort(([a], [b]) => a.localeCompare(b)),
);
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold">{{ t('gradebook.tabs.lessons') }}</h3>
    </div>

    <WeekPicker v-model="currentMonday" />

    <InfoBanner :description="t('gradebook.lessons.infoBanner')" />

    <EmptyLoading v-if="isFetching && !lessons" />

    <EmptyLoadingError v-else-if="isError" @refresh="refetch" />

    <template v-else-if="lessons && lessons.length > 0">
      <LessonDayCard
        v-for="[dateKey, dayLessons] in sortedDays"
        :key="dateKey"
        :date="dayLessons[0]!.date"
        :lessons="dayLessons"
      />
    </template>

    <Empty v-else>
      <EmptyHeader>
        <EmptyMedia variant="icon">
          <BookOpenCheck />
        </EmptyMedia>
        <EmptyTitle>{{ t('gradebook.lessons.emptyTitle') }}</EmptyTitle>
      </EmptyHeader>
      <EmptyContent>
        <p class="text-muted-foreground">
          {{ t('gradebook.lessons.emptyDescription') }}
        </p>
      </EmptyContent>
    </Empty>
  </div>
</template>
