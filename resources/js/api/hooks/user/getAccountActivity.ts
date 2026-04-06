import { UserService } from '../../services/user';
import { useQuery } from '@tanstack/vue-query';
import { type Ref, unref } from 'vue';

export const useGetAccountActivity = (page: Ref<number>) =>
  useQuery({
    queryKey: ['getAccountActivity', page],
    queryFn: () => UserService.getActvityLog(unref(page)),
  });
