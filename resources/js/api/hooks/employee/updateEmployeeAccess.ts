import { EmployeeService } from '../../services/employee';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useUpdateEmployeeAccess = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['updateEmployeeAccess'],
    mutationFn: ({ id, action }: { id: number; action: 'revoke' | 'regenerate' | 'generate' }) =>
      EmployeeService.updateAccess(id, action),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getEmployeeAccesses'] });
    },
  });
};
