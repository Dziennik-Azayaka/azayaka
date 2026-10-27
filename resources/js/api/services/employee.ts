import { type EmployeeBodyDTO, type EmployeeDTO, employeeFromDTO } from '../dtos/employee';
import type { Employee } from '../types/employee';
import { http } from '@/config/ofetch';

export const EmployeeService = {
  getAll: (): Promise<Employee[]> =>
    http<EmployeeDTO[]>('/employees', {
      method: 'GET',
    }).then((res) => res.map(employeeFromDTO)),
  add: (data: EmployeeBodyDTO) =>
    http('/employees', {
      method: 'POST',
      body: data,
    }),
  changeActivity: (id: number, state: boolean, password: string) =>
    http(`/employees/${id}/activity`, {
      method: 'PUT',
      body: { state, password },
    }),
  edit: (id: number, data: EmployeeBodyDTO) =>
    http(`/employees/${id}`, {
      method: 'PUT',
      body: data,
    }),
};
