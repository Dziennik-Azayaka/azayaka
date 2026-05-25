import { StudentService } from '../../services/student';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useDeleteStudent = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['deleteStudent'],
    mutationFn: (id: number) => StudentService.delete(id),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getFilteredStudents'] });
    },
  });
};
