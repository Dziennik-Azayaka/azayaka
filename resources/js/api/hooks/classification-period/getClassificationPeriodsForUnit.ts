import { ClassificationPeriodService } from '../../services/classification-period';
import { useQuery } from '@tanstack/vue-query';
import { type MaybeRef, computed, toValue } from 'vue';

export const useGetClassificationPeriodsForUnit = (
  unitId: MaybeRef<number | undefined>,
  schoolYear: MaybeRef<number | undefined>,
) =>
  useQuery({
    queryKey: computed(() => [
      'getClassificationPeriodsForUnit',
      toValue(unitId),
      toValue(schoolYear),
    ]),
    queryFn: () =>
      ClassificationPeriodService.getBySchoolUnit(toValue(unitId)!, toValue(schoolYear)!),
    enabled: computed(() => !!toValue(unitId) && !!toValue(schoolYear)),
  });
