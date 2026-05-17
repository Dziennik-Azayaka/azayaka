import { classFromDTO, type ClassBodyDTO, type ClassDTO, type GetClassFilter } from '../dtos/class';
import type { Class } from '../types/class';
import { http } from '@/config/ofetch';

export const ClassService = {
  getAll: (category: GetClassFilter): Promise<Class[]> =>
    http<ClassDTO[]>('/classUnits', {
      method: 'GET',
      query: { category },
    }).then((res) => res.map(classFromDTO)),
  getById: (id: number): Promise<Class> =>
    http<ClassDTO>(`/classUnits/${id}`, { method: 'GET' }).then((res) => classFromDTO(res)),
  add: (data: ClassBodyDTO) =>
    http('/classUnits', {
      method: 'POST',
      body: data
    }),
  delete: (id: number) =>
    http(`/classUnits/${id}`, {
      method: 'DELETE',
    }),
};
