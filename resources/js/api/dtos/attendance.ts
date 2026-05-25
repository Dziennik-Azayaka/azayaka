import type { AttendanceComplexType, DayViewAttendance, DayViewLesson, DayViewStudent } from '../types/attendance'

export interface DayViewStudentDTO {
  id: number
  firstName: string
  secondName: string | null
  lastName: string
  position: number
}

export interface DayViewAttendanceDTO {
  id: number
  primitiveType: number | null
  complexType: number | null
  studentId: number
  employee: string
}

export interface DayViewLessonDTO {
  id: number
  subject: string
  number: number
  startTime: string
  endTime: string
  students: DayViewStudentDTO[]
  attendances: DayViewAttendanceDTO[]
}

export interface AttendanceComplexTypeDTO {
  id: number
  name: string
  shortcut: string
  active: boolean
  primitiveType: number
}

const PRIMITIVE_SHORTCUTS: Record<number, string> = {
  0: 'o',
  1: 'nb',
  2: 'nu',
  3: 's',
  4: 'su',
  5: 'e',
}

export function primitiveTypeToShortcut(type: number | null): string | null {
  if (type === null) return null
  return PRIMITIVE_SHORTCUTS[type] ?? null
}

function studentFromDTO(dto: DayViewStudentDTO): DayViewStudent {
  return { ...dto }
}

function attendanceFromDTO(dto: DayViewAttendanceDTO): DayViewAttendance {
  return {
    id: dto.id,
    primitiveType: primitiveTypeToShortcut(dto.primitiveType),
    complexType: dto.complexType,
    studentId: dto.studentId,
    employee: dto.employee,
  }
}

export function dayViewLessonFromDTO(dto: DayViewLessonDTO): DayViewLesson {
  return {
    ...dto,
    students: dto.students.map(studentFromDTO),
    attendances: dto.attendances.map(attendanceFromDTO),
  }
}

export function complexTypeFromDTO(dto: AttendanceComplexTypeDTO): AttendanceComplexType {
  return {
    ...dto,
    primitiveType: primitiveTypeToShortcut(dto.primitiveType) ?? '',
  }
}
