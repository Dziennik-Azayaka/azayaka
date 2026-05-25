import { FulfillmentService, type FulfillmentBody } from '../../services/fulfillment';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useCreateFulfillment = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['createFulfillment'],
    mutationFn: ({ childId, data }: { childId: number; data: FulfillmentBody }) =>
      FulfillmentService.create(childId, data),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getFilteredChildren'] });
    },
  });
};
