import { ClassificationPeriodService } from '../../services/classification-period';
import { useQuery } from '@tanstack/vue-query';
import type { Ref } from 'vue';

export const useGetClassificationPeriods = (unitIds: Ref<number[]>, schoolYear: Ref<number>) =>
  useQuery({
    queryKey: ['getClassificationPeriods', unitIds, schoolYear],
    queryFn: async () =>
      await Promise.all(
        unitIds.value.map(async (unitId) => ({
          unitId,
          periods: await ClassificationPeriodService.getBySchoolUnit(unitId, schoolYear.value),
        })),
      ),
  });
