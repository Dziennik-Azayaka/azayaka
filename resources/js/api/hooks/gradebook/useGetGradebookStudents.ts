import { GradebookService } from '../../services/gradebook';
import { useQuery } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRef } from 'vue';

export const useGetGradebookStudents = (gradebookId: MaybeRef<number | undefined>) =>
  useQuery({
    queryKey: computed(() => ['getGradebookStudents', toValue(gradebookId)]),
    queryFn: () => GradebookService.listStudents(toValue(gradebookId)!),
    enabled: computed(() => !!toValue(gradebookId)),
  });
