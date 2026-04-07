import { classFromDTO, type ClassDTO, type GetClassFilter } from '../dtos/class';
import type { Class } from '../types/class';
import { http } from '@/config/ofetch';

export const ClassService = {
  getAll: (category: GetClassFilter): Promise<Class[]> =>
    http<ClassDTO[]>('/schoolUnits/all/classUnits', {
      method: 'GET',
      query: { category }
    }).then((res) => res.map(classFromDTO)),
};
