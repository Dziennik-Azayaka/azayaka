import type { ClassBodyDTO } from '@/api/dtos/class';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { ClassService } from '@/api/services/class';

export const useAddClass = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['addClass'],
    mutationFn: (body: ClassBodyDTO) => ClassService.add(body),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getClasses'] });
      await queryClient.invalidateQueries({ queryKey: ['getClassById'] });
    },
  });
};
