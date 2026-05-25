import { StudentService } from '../../services/student';
import { useQuery } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRef } from 'vue';

export const useGetStudents = (registryId: MaybeRef<number | undefined>) => {
  const resolvedId = computed(() => toValue(registryId));

  return useQuery({
    queryKey: computed(() => ['getStudents', resolvedId.value]),
    queryFn: () => StudentService.getFiltered(resolvedId.value!, 1, undefined, null, 'active'),
    enabled: computed(() => !!resolvedId.value),
    staleTime: 0,
  });
};
