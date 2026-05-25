import { GradebookService } from '@/api/services/gradebook';
import { useQuery } from '@tanstack/vue-query';
import { type MaybeRef, computed, toValue } from 'vue';

export const useGetGradebookGroups = (gradebookId: MaybeRef<number | undefined>) =>
  useQuery({
    queryKey: computed(() => ['getGradebookGroups', toValue(gradebookId)]),
    queryFn: () => GradebookService.listGroups(toValue(gradebookId)!),
    enabled: computed(() => !!toValue(gradebookId)),
  });
