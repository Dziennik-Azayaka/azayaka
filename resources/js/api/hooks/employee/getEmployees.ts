import { EmployeeService } from '../../services/employee';
import { useQuery } from '@tanstack/vue-query';

export const useGetEmployees = () =>
  useQuery({
    queryKey: ['getEmployees'],
    queryFn: () => EmployeeService.getAll(),
  });
