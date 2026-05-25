import { FulfillmentService } from '../../services/fulfillment';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useDeleteFulfillment = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['deleteFulfillment'],
    mutationFn: ({ childId, fulfillmentId }: { childId: number; fulfillmentId: number }) =>
      FulfillmentService.delete(childId, fulfillmentId),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getFilteredChildren'] });
    },
  });
};
