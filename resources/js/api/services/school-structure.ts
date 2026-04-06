import { type SchoolComplexDTO, schoolStructureFromDTOs } from '../dtos/school-complex';
import { type SchoolUnitBodyDTO, type SchoolUnitDTO, schoolUnitFromDTO } from '../dtos/school-unit';
import type { SchoolUnit } from '../types/school-structure';
import { http } from '@/config/ofetch';

export const SchoolStructureService = {
  getSchoolUnits: (): Promise<SchoolUnit[]> =>
    http<SchoolUnitDTO[]>('/schoolUnits', { method: 'GET' }).then((res) =>
      res.map(schoolUnitFromDTO),
    ),
  getStructure: async () => {
    const [complexes, units] = await Promise.all([
      http<SchoolComplexDTO[]>('/schoolComplex', { method: 'GET' }),
      http<SchoolUnitDTO[]>('/schoolUnits', { method: 'GET' }),
    ]);
    return schoolStructureFromDTOs(complexes.length ? complexes[0]! : null, units);
  },
  createSchoolUnit: (data: SchoolUnitBodyDTO) =>
    http('/schoolUnits', { method: 'POST', body: data }),
  editSchoolUnit: (id: number, data: SchoolUnitBodyDTO) =>
    http(`/schoolUnits/${id}`, { method: 'PUT', body: data }),
  createSchoolComplex: (name: string) => http('/schoolComplex', { method: 'POST', body: { name } }),
  editSchoolComplex: (id: number, name: string) =>
    http(`/schoolComplex/${id}`, { method: 'PUT', body: { name, type: 90 } }),
  setSchoolUnitActivity: (id: number, state: boolean, password: string) =>
    http(`/schoolUnits/${id}/activity`, {
      method: 'PUT',
      body: { state, password },
    }),
};
