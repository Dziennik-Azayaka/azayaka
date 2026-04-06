import { ClassificationPeriodService } from '../../services/classification-period';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useSetClassificationPeriods = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['setClassificationPeriods'],
    mutationFn: ({
      unitId,
      schoolYear,
      ends,
    }: {
      unitId: number;
      schoolYear: number;
      ends: string[];
    }) => ClassificationPeriodService.setForSchoolUnit(unitId, schoolYear, ends),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getClassificationPeriods'] });
    },
  });
};
