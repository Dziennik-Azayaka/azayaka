import { http } from '@/config/ofetch';

export const StudentRegistryService = {
  getIdBySchoolUnit: (unitId: number): Promise<number> =>
    http<{ registryId: number }>(`/studentRegistry/lookup/${unitId}`, {
      method: 'GET',
    }).then((res) => res.registryId),
  create: (unitId: number) =>
    http('/studentRegistry', { method: 'POST', body: { schoolUnitId: unitId } }),
};
