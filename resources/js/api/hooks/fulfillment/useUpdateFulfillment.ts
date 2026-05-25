import { FulfillmentService, type FulfillmentBody } from '../../services/fulfillment';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useUpdateFulfillment = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['updateFulfillment'],
    mutationFn: ({
      childId,
      fulfillmentId,
      data,
    }: {
      childId: number;
      fulfillmentId: number;
      data: FulfillmentBody;
    }) => FulfillmentService.update(childId, fulfillmentId, data),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getFilteredChildren'] });
    },
  });
};
