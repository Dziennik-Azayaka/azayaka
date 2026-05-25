export interface DayViewStudent {
  id: number
  firstName: string
  secondName: string | null
  lastName: string
  position: number
}

export interface DayViewAttendance {
  id: number
  primitiveType: string | null
  complexType: number | null
  studentId: number
  employee: string
}

export interface DayViewLesson {
  id: number
  subject: string
  number: number
  startTime: string
  endTime: string
  students: DayViewStudent[]
  attendances: DayViewAttendance[]
}

export interface AttendanceComplexType {
  id: number
  name: string
  shortcut: string
  active: boolean
  primitiveType: string
}
