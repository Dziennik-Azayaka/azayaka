import { EmployeeService } from '../../services/employee';
import type { EmployeeBodyDTO } from '@/api/dtos/employee';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useAddEmployee = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['addEmploye'],
    mutationFn: (body: EmployeeBodyDTO) => EmployeeService.add(body),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getEmployees'] });
    },
  });
};
