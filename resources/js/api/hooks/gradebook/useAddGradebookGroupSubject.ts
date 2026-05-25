import { GradebookService } from '@/api/services/gradebook';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { type MaybeRef, toValue } from 'vue';

export const useAddGradebookGroupSubject = (gradebookId: MaybeRef<number | undefined>) => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['addGradebookGroupSubject'],
    mutationFn: (params: {
      groupId: number;
      subjectId: number;
      description: string;
      teacherIds?: number[];
    }) =>
      GradebookService.addSubjectToGroup(
        params.groupId,
        params.subjectId,
        params.description,
        params.teacherIds,
      ),
    onSuccess: async () => {
      await queryClient.invalidateQueries({
        queryKey: ['getGradebookGroups', toValue(gradebookId)!],
      });
    },
  });
};
