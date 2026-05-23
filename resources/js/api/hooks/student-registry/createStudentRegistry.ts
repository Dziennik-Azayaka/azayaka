import { StudentRegistryService } from '@/api/services/student-registry';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useCreateStudentRegistry = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['createStudentRegistry'],
    mutationFn: (unitId: number) => StudentRegistryService.create(unitId),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getStudentRegistryId'] });
    },
  });
};
