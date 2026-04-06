import { EmployeeService } from '../../services/employee';
import type { EmployeeBodyDTO } from '@/api/dtos/employee';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useEditEmployee = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['editEmployee'],
    mutationFn: ({ id, body }: { id: number; body: EmployeeBodyDTO }) =>
      EmployeeService.edit(id, body),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getEmployees'] });
    },
  });
};
