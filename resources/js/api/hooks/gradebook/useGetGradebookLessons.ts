import { LessonService } from '../../services/lesson';
import { useQuery } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRef } from 'vue';

export const useGetGradebookLessons = (
  gradebookId: MaybeRef<number | undefined>,
  dateFrom: MaybeRef<string | undefined>,
  dateTo: MaybeRef<string | undefined>,
) =>
  useQuery({
    queryKey: computed(() => [
      'getGradebookLessons',
      toValue(gradebookId),
      toValue(dateFrom),
      toValue(dateTo),
    ]),
    queryFn: () =>
      LessonService.getByGradebook(toValue(gradebookId)!, {
        dateFrom: toValue(dateFrom),
        dateTo: toValue(dateTo),
        completed: true,
      }),
    enabled: computed(
      () => !!toValue(gradebookId) && !!toValue(dateFrom) && !!toValue(dateTo),
    ),
  });
