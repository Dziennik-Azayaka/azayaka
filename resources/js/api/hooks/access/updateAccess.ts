import { AccessService } from '../../services/access';
import type { AccessAction, AccessEntry } from '@/api/types/access';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useUpdateAccess = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['updateAccess'],
    mutationFn: ({ entry, action }: { entry: AccessEntry; action: AccessAction }) =>
      AccessService.update(entry, action),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getAccesses'] });
    },
  });
};
