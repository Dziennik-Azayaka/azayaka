import { ChildService } from '../../services/child';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useDeleteChild = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['deleteChild'],
    mutationFn: (id: number) => ChildService.delete(id),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getFilteredChildren'] });
    },
  });
};
