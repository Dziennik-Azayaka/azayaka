import { GradebookService } from '@/api/services/gradebook';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { type MaybeRef, toValue } from 'vue';

export const useUpdateGradebookGroupSubject = (gradebookId: MaybeRef<number | undefined>) => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['updateGradebookGroupSubject'],
    mutationFn: (params: {
      groupId: number;
      subjectId: number;
      description: string;
      teacherIds: number[];
    }) =>
      GradebookService.updateGroupSubject(
        params.groupId,
        params.subjectId,
        params.description,
      ).then(() =>
        GradebookService.updateGroupSubjectTeachers(
          params.groupId,
          params.subjectId,
          params.teacherIds,
        ),
      ),
    onSuccess: async () => {
      await queryClient.invalidateQueries({
        queryKey: ['getGradebookGroups', toValue(gradebookId)!],
      });
    },
  });
};
