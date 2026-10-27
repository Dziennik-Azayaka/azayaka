import { AccessService } from '../../services/access';
import type { AccessType } from '@/api/types/access';
import { useQuery } from '@tanstack/vue-query';

export const useGetAccesses = (type: AccessType) =>
  useQuery({
    queryKey: ['getAccesses', type],
    queryFn: () => AccessService.getAll(type),
  });
