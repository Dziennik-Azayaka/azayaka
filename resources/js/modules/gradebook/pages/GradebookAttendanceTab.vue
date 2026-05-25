<script setup lang="ts">
import { useGetAttendanceComplexTypes } from '@/api/hooks/attendance/useGetAttendanceComplexTypes'
import { useGetAttendanceDayView } from '@/api/hooks/attendance/useGetAttendanceDayView'
import type { DayViewLesson, DayViewStudent } from '@/api/types/attendance'
import { Empty, EmptyContent, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty'
import { EmptyLoading } from '@/components/ui/empty'
import EmptyLoadingError from '@/components/ui/empty/EmptyLoadingError.vue'
import TableTemplate from '@/components/ui/table/TableTemplate.vue'
import { useGradebookStore } from '@/stores/gradebook'
import {
  createColumnHelper,
  getCoreRowModel,
  useVueTable,
  type ColumnDef,
} from '@tanstack/vue-table'
import { ClipboardList } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import DayPicker from '../components/DayPicker.vue'

const { t } = useI18n()
const gradebookStore = useGradebookStore()

const gradebookId = computed(() => gradebookStore.selectedGradebook?.id)

function formatDate(date: Date): string {
  const y = date.getFullYear()
  const m = String(date.getMonth() + 1).padStart(2, '0')
  const d = String(date.getDate()).padStart(2, '0')
  return `${y}-${m}-${d}`
}

const currentDate = ref(new Date())
const dateStr = computed(() => formatDate(currentDate.value))

const {
  data: lessons,
  isFetching,
  isError,
  refetch,
} = useGetAttendanceDayView(gradebookId, dateStr)

const { data: complexTypes } = useGetAttendanceComplexTypes()

const complexTypeMap = computed(() => {
  const map = new Map<number, string>()
  for (const ct of complexTypes.value ?? []) {
    map.set(ct.id, ct.shortcut)
  }
  return map
})

function getAttendanceShortcut(
  lesson: DayViewLesson,
  studentId: number,
  complexMap: Map<number, string>,
): string {
  const attendance = lesson.attendances.find((a) => a.studentId === studentId)
  if (!attendance) return ''
  if (attendance.primitiveType !== null) return attendance.primitiveType
  if (attendance.complexType !== null) return complexMap.get(attendance.complexType) ?? ''
  return ''
}

function getStudentDisplayName(student: DayViewStudent): string {
  const parts = [student.lastName, student.firstName]
  if (student.secondName) parts.push(student.secondName)
  return parts.join(' ')
}

interface AttendanceRow {
  position: number
  studentId: number
  displayName: string
  lessonAttendances: Record<number, string>
}

const columnHelper = createColumnHelper<AttendanceRow>()

const rows = computed<AttendanceRow[]>(() => {
  if (!lessons.value || lessons.value.length === 0) return []

  const firstLesson = lessons.value![0]!
  const complexMap = complexTypeMap.value

  const students = [...firstLesson.students].sort((a, b) => a.position - b.position)

  return students.map((student) => {
    const lessonAttendances: Record<number, string> = {}

    for (const lesson of lessons.value!) {
      lessonAttendances[lesson.id] = getAttendanceShortcut(lesson, student.id, complexMap)
    }

    return {
      position: student.position,
      studentId: student.id,
      displayName: getStudentDisplayName(student),
      lessonAttendances,
    }
  })
})

const columns = computed(() => {
  const cols: ColumnDef<AttendanceRow>[] = [
    columnHelper.accessor('position', {
      header: () => t('gradebook.attendance.position'),
      size: 64,
    }),
    columnHelper.accessor('displayName', {
      id: 'studentName',
      header: () => t('gradebook.attendance.student'),
      size: 240,
    }),
  ]

  for (const lesson of lessons.value ?? []) {
    const lessonId = lesson.id
    const timeLabel = `${lesson.startTime.slice(0, 5)} – ${lesson.endTime.slice(0, 5)}`
    cols.push(
      columnHelper.accessor(
        (row) => row.lessonAttendances[lessonId] ?? '',
        {
          id: `lesson_${lessonId}`,
          header: () => timeLabel,
          size: 80,
        },
      ),
    )
  }

  return cols
})

const table = useVueTable({
  get data() {
    return rows.value
  },
  get columns() {
    return columns.value
  },
  getRowId: (row) => row.studentId.toString(),
  getCoreRowModel: getCoreRowModel(),
})
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold">{{ t('gradebook.tabs.attendance') }}</h3>
    </div>

    <DayPicker v-model="currentDate" />

    <EmptyLoading v-if="isFetching && !lessons" />

    <EmptyLoadingError v-else-if="isError" @refresh="refetch" />

    <template v-else-if="lessons && lessons.length > 0">
      <TableTemplate :table="table" />
    </template>

    <Empty v-else>
      <EmptyHeader>
        <EmptyMedia variant="icon">
          <ClipboardList />
        </EmptyMedia>
        <EmptyTitle>{{ t('gradebook.attendance.emptyTitle') }}</EmptyTitle>
      </EmptyHeader>
      <EmptyContent>
        <p class="text-muted-foreground">
          {{ t('gradebook.attendance.emptyDescription') }}
        </p>
      </EmptyContent>
    </Empty>
  </div>
</template>
