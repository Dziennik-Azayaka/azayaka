import type { GetClassFilter } from '@/api/dtos/class';
import { ClassService } from '@/api/services/class';
import { useQuery } from '@tanstack/vue-query';
import type { ComputedRef } from 'vue';

export const useGetClasses = (filter: ComputedRef<GetClassFilter>) =>
  useQuery({
    queryKey: ['getClasses', filter],
    queryFn: () => ClassService.getAll(filter.value)
  });
