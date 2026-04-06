import { EmployeeService } from '../../services/employee';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useSetEmployeeActivity = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['setEmployeeActivity'],
    mutationFn: ({ id, state, password }: { id: number; state: boolean; password: string }) =>
      EmployeeService.changeActivity(id, state, password),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getEmployees'] });
    },
  });
};
