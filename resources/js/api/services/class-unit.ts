import { type ClassUnitDTO, classUnitFromDTO } from '../dtos/class-unit';
import type { ClassUnit } from '../types/class-unit';
import { http } from '@/config/ofetch';

export const ClassUnitService = {
  list: (): Promise<ClassUnit[]> =>
    http<ClassUnitDTO[]>('/classUnits', { method: 'GET' }).then((res) => res.map(classUnitFromDTO)),
};
