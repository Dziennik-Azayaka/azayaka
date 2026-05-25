import { http } from '@/config/ofetch'
import type { AttendanceComplexType } from '../types/attendance'
import { type AttendanceComplexTypeDTO, type DayViewLessonDTO, complexTypeFromDTO, dayViewLessonFromDTO } from '../dtos/attendance'
import type { DayViewLesson } from '../types/attendance'

export const AttendanceService = {
  getDayView(gradebookId: number, date: string): Promise<DayViewLesson[]> {
    return http<DayViewLessonDTO[]>(`/gradebooks/${gradebookId}/attendance/dayView`, {
      method: 'GET',
      query: { date },
    }).then((res) => res.map(dayViewLessonFromDTO))
  },

  getComplexTypes(): Promise<AttendanceComplexType[]> {
    return http<AttendanceComplexTypeDTO[]>('/attendanceComplexTypes', {
      method: 'GET',
    }).then((res) => res.map(complexTypeFromDTO))
  },
}
