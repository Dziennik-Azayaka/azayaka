import { EmployeeService } from '../../services/employee';
import { useQuery } from '@tanstack/vue-query';

export const useGetEmployeeAccesses = () =>
  useQuery({
    queryKey: ['getEmployeeAccesses'],
    queryFn: () => EmployeeService.getAccesses(),
  });
