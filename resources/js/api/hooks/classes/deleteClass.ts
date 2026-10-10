import { ClassService } from '@/api/services/class';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useDeleteClass = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['deleteClass'],
    mutationFn: (id: number) => ClassService.delete(id),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getClasses'] });
      await queryClient.invalidateQueries({ queryKey: ['getClassById'] });
    },
  });
};
