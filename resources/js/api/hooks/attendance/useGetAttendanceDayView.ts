import { AttendanceService } from '../../services/attendance'
import { useQuery } from '@tanstack/vue-query'
import { computed, toValue, type MaybeRef } from 'vue'

export const useGetAttendanceDayView = (
  gradebookId: MaybeRef<number | undefined>,
  date: MaybeRef<string | undefined>,
) =>
  useQuery({
    queryKey: computed(() => [
      'getAttendanceDayView',
      toValue(gradebookId),
      toValue(date),
    ]),
    queryFn: () =>
      AttendanceService.getDayView(toValue(gradebookId)!, toValue(date)!),
    enabled: computed(() => !!toValue(gradebookId) && !!toValue(date)),
  })
