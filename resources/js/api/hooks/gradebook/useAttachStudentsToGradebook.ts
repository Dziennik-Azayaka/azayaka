import { GradebookService } from '../../services/gradebook';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useAttachStudentsToGradebook = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({
      gradebookId,
      studentIds,
      positions,
    }: {
      gradebookId: number;
      studentIds: number[];
      positions: number[];
    }) => GradebookService.attachStudents(gradebookId, studentIds, positions),
    onSuccess: (_, { gradebookId }) => {
      queryClient.invalidateQueries({ queryKey: ['getGradebookStudents', gradebookId] });
    },
  });
};
