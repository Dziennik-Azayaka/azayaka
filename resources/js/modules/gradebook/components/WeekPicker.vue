<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

const props = defineProps<{
  modelValue: Date;
}>();

const emit = defineEmits<{
  'update:modelValue': [value: Date];
}>();

function getMondayOfWeek(date: Date): Date {
  const d = new Date(date);
  const day = d.getDay();
  const diff = d.getDate() - day + (day === 0 ? -6 : 1);
  d.setDate(diff);
  d.setHours(0, 0, 0, 0);
  return d;
}

function getSundayOfWeek(monday: Date): Date {
  const d = new Date(monday);
  d.setDate(d.getDate() + 6);
  return d;
}

function formatDateShort(date: Date): string {
  return date.toLocaleDateString(locale.value, { day: 'numeric', month: 'numeric' });
}

function formatDateFull(date: Date): string {
  return date.toLocaleDateString(locale.value, {
    day: 'numeric',
    month: 'numeric',
    year: 'numeric',
  });
}

const monday = computed(() => getMondayOfWeek(props.modelValue));
const sunday = computed(() => getSundayOfWeek(monday.value));

const currentWeekMonday = computed(() => getMondayOfWeek(new Date()));
const isCurrentWeek = computed(
  () => monday.value.getTime() === currentWeekMonday.value.getTime(),
);

const weekLabel = computed(() => {
  const from = formatDateShort(monday.value);
  const to = formatDateFull(sunday.value);
  return `${from} – ${to}`;
});

function goBack() {
  const d = new Date(monday.value);
  d.setDate(d.getDate() - 7);
  emit('update:modelValue', d);
}

function goForward() {
  const d = new Date(monday.value);
  d.setDate(d.getDate() + 7);
  emit('update:modelValue', d);
}

function goToToday() {
  emit('update:modelValue', new Date());
}
</script>

<template>
  <div class="flex items-center gap-2">
    <Button variant="outline" size="icon" @click="goBack">
      <ChevronLeft />
    </Button>

    <span class="min-w-40 text-center text-sm font-medium tabular-nums">
      {{ weekLabel }}
    </span>

    <Button variant="outline" size="icon" @click="goForward">
      <ChevronRight />
    </Button>

    <Button v-if="!isCurrentWeek" variant="ghost" size="sm" @click="goToToday">
      {{ t('gradebook.lessons.today') }}
    </Button>
  </div>
</template>
