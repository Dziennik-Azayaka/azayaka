import { ClassificationPeriodService } from '../../services/classification-period';
import { useQuery } from '@tanstack/vue-query';
import type { Ref } from 'vue';

export const useGetClassificationPeriodsByUnit = (unitId: Ref<number>, schoolYear: Ref<number>) =>
  useQuery({
    queryKey: ['getClassificationPeriodsByUnit', unitId, schoolYear],
    queryFn: () => ClassificationPeriodService.getBySchoolUnit(unitId.value, schoolYear.value),
  });
