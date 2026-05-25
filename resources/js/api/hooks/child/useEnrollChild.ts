import { ChildService } from '../../services/child';
import { useMutation } from '@tanstack/vue-query';

export const useEnrollChild = () =>
  useMutation({
    mutationKey: ['enrollChild'],
    mutationFn: (data: { childrenRegistryId: number; personId: number }) =>
      ChildService.enroll(data.childrenRegistryId, { personId: data.personId }),
  });
