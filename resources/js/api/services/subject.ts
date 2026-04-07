import type { SubjectBodyDTO, SubjectDTO } from '../dtos/subject';
import type { Subject } from '../types/subject';
import { http } from '@/config/ofetch';

export const SubjectService = {
  getAll: (): Promise<Subject[]> =>
    http<SubjectDTO[]>('/subjects', {
      method: 'GET',
    }),
  add: (data: SubjectBodyDTO) =>
    http('/subjects', {
      method: 'POST',
      body: data,
    }),
  edit: (id: number, data: SubjectBodyDTO) =>
    http(`/subjects/${id}`, {
      method: 'PUT',
      body: data,
    }),
  toggleActivity: (id: number) =>
    http(`/subjects/${id}/activity`, {
      method: 'PUT',
    }),
};
