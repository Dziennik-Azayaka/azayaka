import { GradebookService } from '@/api/services/gradebook';
import { useQuery } from '@tanstack/vue-query';
import { type MaybeRef, computed, toValue } from 'vue';

export const useGetGradebooks = (
  schoolUnitId: MaybeRef<number | undefined>,
  classUnitId: MaybeRef<number | undefined>,
  schoolYear: MaybeRef<number | undefined>,
) =>
  useQuery({
    queryKey: computed(() => [
      'getGradebooks',
      toValue(schoolUnitId),
      toValue(classUnitId),
      toValue(schoolYear),
    ]),
    queryFn: () =>
      GradebookService.list(toValue(schoolUnitId)!, {
        classUnitId: toValue(classUnitId),
        schoolYear: toValue(schoolYear),
      }),
    enabled: computed(
      () => !!toValue(schoolUnitId) && !!toValue(classUnitId) && !!toValue(schoolYear),
    ),
  });
