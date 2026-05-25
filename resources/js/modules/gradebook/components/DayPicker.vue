<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const props = defineProps<{
  modelValue: Date
}>()

const emit = defineEmits<{
  'update:modelValue': [value: Date]
}>()

function isSameDay(a: Date, b: Date): boolean {
  return (
    a.getFullYear() === b.getFullYear() &&
    a.getMonth() === b.getMonth() &&
    a.getDate() === b.getDate()
  )
}

const isToday = computed(() => isSameDay(props.modelValue, new Date()))

const dateLabel = computed(() =>
  props.modelValue.toLocaleDateString(locale.value, {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  }),
)

function goBack() {
  const d = new Date(props.modelValue)
  d.setDate(d.getDate() - 1)
  emit('update:modelValue', d)
}

function goForward() {
  const d = new Date(props.modelValue)
  d.setDate(d.getDate() + 1)
  emit('update:modelValue', d)
}

function goToToday() {
  emit('update:modelValue', new Date())
}
</script>

<template>
  <div class="flex items-center gap-2">
    <Button variant="outline" size="icon" @click="goBack">
      <ChevronLeft />
    </Button>

    <span class="min-w-52 text-center text-sm font-medium tabular-nums">
      {{ dateLabel }}
    </span>

    <Button variant="outline" size="icon" @click="goForward">
      <ChevronRight />
    </Button>

    <Button v-if="!isToday" variant="ghost" size="sm" @click="goToToday">
      {{ t('gradebook.attendance.today') }}
    </Button>
  </div>
</template>
