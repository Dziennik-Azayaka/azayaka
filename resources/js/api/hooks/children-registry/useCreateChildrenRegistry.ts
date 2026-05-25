import { ChildrenRegistryService } from '@/api/services/children-registry';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useCreateChildrenRegistry = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['createChildrenRegistry'],
    mutationFn: (unitId: number) => ChildrenRegistryService.create(unitId),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getChildrenRegistryId'] });
    },
  });
};
