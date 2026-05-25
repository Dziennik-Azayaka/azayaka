import { GradebookService } from '@/api/services/gradebook';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { type MaybeRef, toValue } from 'vue';

export const useDeleteGradebookGroupSubject = (gradebookId: MaybeRef<number | undefined>) => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['deleteGradebookGroupSubject'],
    mutationFn: (params: { groupId: number; subjectId: number }) =>
      GradebookService.deleteGroupSubject(params.groupId, params.subjectId),
    onSuccess: async () => {
      await queryClient.invalidateQueries({
        queryKey: ['getGradebookGroups', toValue(gradebookId)!],
      });
    },
  });
};
