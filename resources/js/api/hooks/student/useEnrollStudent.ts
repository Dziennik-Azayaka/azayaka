import { StudentService } from '../../services/student';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useEnrollStudent = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['enrollStudent'],
    mutationFn: (data: { registryId: number; personId: number; admissionDate: string }) =>
      StudentService.enroll(data.registryId, {
        personId: data.personId,
        admissionDate: data.admissionDate,
      }),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getFilteredStudents'] });
    },
  });
};
