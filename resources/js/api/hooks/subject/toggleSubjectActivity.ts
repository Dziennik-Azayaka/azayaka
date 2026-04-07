import { SubjectService } from '../../services/subject';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useToggleSubjectActivity = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['toggleSubjectActivity'],
    mutationFn: (id: number) => SubjectService.toggleActivity(id),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getSubjects'] });
    },
  });
};
