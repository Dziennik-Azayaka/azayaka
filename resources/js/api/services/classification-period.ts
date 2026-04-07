import {
  type ClassificationPeriodDTO,
  classificationPeriodFromDTO,
} from '../dtos/classification-period';
import type { ClassificationPeriod } from '../types/classification-period';
import { http } from '@/config/ofetch';

export const ClassificationPeriodService = {
  getBySchoolUnit: (unitId: number, schoolYear: number): Promise<ClassificationPeriod[]> =>
    http<ClassificationPeriodDTO[]>(`/schoolUnits/${unitId}/classificationPeriods/${schoolYear}`, {
      method: 'GET',
    }).then((res) => res.map(classificationPeriodFromDTO)),
  setForSchoolUnit: (unitId: number, schoolYear: number, ends: string[]) =>
    http(`/schoolUnits/${unitId}/classificationPeriods/${schoolYear}`, {
      method: 'POST',
      body: { periodEnd: ends },
    }),
};
