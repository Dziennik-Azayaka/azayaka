import { StudentService } from '../../services/student';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useUpdateStudent = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['updateStudent'],
    mutationFn: ({
      id,
      body,
    }: {
      id: number;
      body: { admissionDate: string; leaveDate?: string | null; leaveReason?: string | null };
    }) => StudentService.update(id, body),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getFilteredStudents'] });
    },
  });
};
