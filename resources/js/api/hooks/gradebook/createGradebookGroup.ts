import { GradebookService } from '@/api/services/gradebook';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { type MaybeRef, toValue } from 'vue';

export const useCreateGradebookGroup = (gradebookId: MaybeRef<number | undefined>) => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['createGradebookGroup', toValue(gradebookId)],
    mutationFn: (params: { name: string; shortcut: string }) =>
      GradebookService.createGroup(toValue(gradebookId)!, params.name, params.shortcut),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getGradebookGroups', toValue(gradebookId)!] });
    },
  });
};
