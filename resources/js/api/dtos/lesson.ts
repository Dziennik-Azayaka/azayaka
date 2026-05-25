import type { Lesson } from '../types/lesson';

export interface TeacherDTO {
  id: number;
  firstName: string;
  secondName: string | null;
  lastName: string;
}

export interface GroupDTO {
  id: number;
  name: string;
  shortcut: string;
}

export interface LessonDTO {
  id: number;
  number: number;
  date: string;
  primaryTeacher: TeacherDTO;
  assistingTeachers: TeacherDTO[];
  groups: GroupDTO[];
  subject: string;
  topic: string;
  startTime: string;
  endTime: string;
  completed: boolean;
}

export interface LessonQueryParams {
  dateFrom?: string;
  dateTo?: string;
  completed?: boolean;
  subjectId?: number;
  primaryTeacherId?: number;
  topic?: string;
}

export const lessonFromDTO = (dto: LessonDTO): Lesson => ({
  ...dto,
  date: new Date(dto.date),
});
