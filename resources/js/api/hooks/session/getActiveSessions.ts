import { SessionService } from '../../services/session';
import { useQuery } from '@tanstack/vue-query';

export const useGetActiveSessions = () =>
  useQuery({
    queryKey: ['getActiveSessions'],
    queryFn: () => SessionService.getActive(),
  });
