<script setup lang="ts">
import type { Lesson } from '@/api/types/lesson';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

const props = defineProps<{
  date: Date;
  lessons: Lesson[];
}>();

const dayLabel = computed(() =>
  props.date.toLocaleDateString(locale.value, { weekday: 'long', day: 'numeric', month: 'long' }),
);

const lessonCount = computed(() => props.lessons.length);
</script>

<template>
  <div class="rounded-lg border bg-card text-card-foreground">
    <div class="flex items-center justify-between border-b px-6 py-4">
      <h3 class="text-lg font-semibold leading-none tracking-tight">
        {{ dayLabel }}
      </h3>
      <span class="text-sm text-muted-foreground">
        {{ t('gradebook.lessons.lessonCount', { count: lessonCount }) }}
      </span>
    </div>
    <div class="overflow-auto">
      <table class="w-full table-fixed caption-bottom text-sm">
        <thead>
          <tr class="border-b">
            <th class="h-12 px-4 text-left font-medium text-muted-foreground w-[200px]">
              {{ t('gradebook.lessons.time') }}
            </th>
            <th class="h-12 px-4 text-left font-medium text-muted-foreground w-[240px]">
              {{ t('gradebook.lessons.subject') }}
            </th>
            <th class="h-12 px-4 text-left font-medium text-muted-foreground">
              {{ t('gradebook.lessons.topic') }}
            </th>
            <th class="h-12 px-4 text-left font-medium text-muted-foreground w-[300px]">
              {{ t('gradebook.lessons.teacher') }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="lesson in lessons"
            :key="lesson.id"
            class="border-b last:border-b-0"
          >
            <td class="p-4 align-top tabular-nums">
              {{ lesson.startTime.slice(0, 5) }} – {{ lesson.endTime.slice(0, 5) }}
            </td>
            <td class="p-4 align-top">{{ lesson.subject }}</td>
            <td class="p-4 align-top">{{ lesson.topic }}</td>
            <td class="p-4 align-top">
              <div>{{ lesson.primaryTeacher.firstName }} {{ lesson.primaryTeacher.lastName }}</div>
              <div
                v-if="lesson.assistingTeachers.length > 0"
                class="text-xs text-muted-foreground"
              >
                {{ t('gradebook.lessons.withTeachers') }}:
                {{ lesson.assistingTeachers.map((t) => `${t.firstName} ${t.lastName}`).join(', ') }}
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
