import { ClassService } from '@/api/services/class';
import { useQuery } from '@tanstack/vue-query';
import type { Ref } from 'vue';

export const useGetClassById = (id: Ref<number>) =>
  useQuery({
    queryKey: ['getClassById', id],
    queryFn: () => ClassService.getById(id.value)
  });
