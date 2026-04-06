import { type EmployeeBodyDTO, type EmployeeDTO, employeeFromDTO } from '../dtos/employee';
import { type EmployeeAccessDTO, employeeAccessFromDTO } from '../dtos/employee-access';
import type { Employee } from '../types/employee';
import type { EmployeeAccess } from '../types/employee-access';
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
  getAccesses: (): Promise<EmployeeAccess[]> =>
    http<EmployeeAccessDTO[]>('/employees/accesses', { method: 'GET' }).then((res) =>
      res.map(employeeAccessFromDTO),
    ),
  updateAccess: (id: number, action: 'generate' | 'regenerate' | 'revoke') =>
    http('/employees/accesses', {
      method: 'PATCH',
      body: { ids: [id], action },
    }),
  getPdfInstructions: (ids: number[]) =>
    http<Blob, 'blob'>('/employees/accesses/document', {
      method: 'POST',
      body: { ids },
      responseType: 'blob',
      parseResponse: undefined,
    }),
};
