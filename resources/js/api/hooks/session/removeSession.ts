import { SessionService } from '../../services/session';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useRemoveSession = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['removeSession'],
    mutationFn: ({ id, password }: { id: string; password: string }) =>
      SessionService.removeById(id, password),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getActiveSessions'] });
      await queryClient.invalidateQueries({ queryKey: ['getAccountActivity'] });
    },
  });
};
