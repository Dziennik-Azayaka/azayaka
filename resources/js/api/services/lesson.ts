import { type LessonDTO, type LessonQueryParams, lessonFromDTO } from '../dtos/lesson';
import type { Lesson } from '../types/lesson';
import { http } from '@/config/ofetch';

export const LessonService = {
  getByGradebook(gradebookId: number, params?: LessonQueryParams): Promise<Lesson[]> {
    return http<LessonDTO[]>(`/gradebooks/${gradebookId}/lessons`, {
      method: 'GET',
      query: params as Record<string, unknown>,
    }).then((res) => res.map(lessonFromDTO));
  },
};
