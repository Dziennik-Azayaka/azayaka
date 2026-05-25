import { http } from '@/config/ofetch';

export const ChildrenRegistryService = {
  create: (schoolUnitId: number): Promise<{ success: true }> =>
    http('/childrenRegistry', { method: 'POST', body: { schoolUnitId } }),
};
