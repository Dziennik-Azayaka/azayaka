import { http } from '@/config/ofetch';
import { useQuery } from '@tanstack/vue-query';
import { type MaybeRef, computed, toValue } from 'vue';

interface ChildrenRegistryDTO {
  id: number;
  schoolUnitId: number;
}

export const useGetChildrenRegistryId = (unitId: MaybeRef<number | undefined>) =>
  useQuery({
    queryKey: computed(() => ['getChildrenRegistryId', toValue(unitId)]),
    queryFn: async () => {
      const registries = await http<ChildrenRegistryDTO[]>('/childrenRegistry', {
        method: 'GET',
      });
      return registries.find((r) => r.schoolUnitId === toValue(unitId))?.id ?? null;
    },
    enabled: computed(() => !!toValue(unitId)),
  });
