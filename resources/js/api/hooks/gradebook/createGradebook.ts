import { GradebookService } from '@/api/services/gradebook';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useCreateGradebook = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['createGradebook'],
    mutationFn: (params: { classificationPeriodId: number; classUnitId: number }) =>
      GradebookService.create(params.classificationPeriodId, params.classUnitId),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getGradebooks'] });
    },
  });
};
