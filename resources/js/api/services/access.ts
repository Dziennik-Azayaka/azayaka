import {
  type EmployeeAccessDTO,
  type GuardianAccessDTO,
  type StudentAccessDTO,
  employeeAccessFromDTO,
  guardianAccessFromDTO,
  studentAccessFromDTO,
} from '../dtos/access';
import type { AccessAction, AccessEntry, AccessType } from '../types/access';
import { http } from '@/config/ofetch';

const endpoints: Record<AccessType, { list: string; document: string }> = {
  employee: {
    list: '/employees/accesses',
    document: '/employees/accesses/document',
  },
  student: {
    list: '/students/accesses',
    document: '/students/accesses/document',
  },
  guardian: {
    list: '/guardians/accesses',
    document: '/guardians/accesses/document',
  },
};

export const AccessService = {
  getAll: async (type: AccessType): Promise<AccessEntry[]> => {
    switch (type) {
      case 'employee':
        return (await http<EmployeeAccessDTO[]>(endpoints.employee.list, { method: 'GET' })).map(
          employeeAccessFromDTO,
        );
      case 'student':
        return (await http<StudentAccessDTO[]>(endpoints.student.list, { method: 'GET' })).map(
          studentAccessFromDTO,
        );
      case 'guardian':
        return (await http<GuardianAccessDTO[]>(endpoints.guardian.list, { method: 'GET' })).map(
          guardianAccessFromDTO,
        );
    }
  },

  update: (entry: AccessEntry, action: AccessAction) => {
    if (entry.type === 'employee')
      return http(endpoints.employee.list, {
        method: 'PATCH',
        body: { ids: [entry.entityId], action },
      });

    if (action === 'revoke')
      throw new Error(`Revoking access is not supported for access type "${entry.type}".`);

    if (entry.type === 'student')
      return http(`/students/${entry.entityId}/generateAccess`, { method: 'GET' });

    if (entry.studentId === undefined) throw new Error('Guardian access requires a student.');

    return http(`/guardians/${entry.entityId}/students/${entry.studentId}/generateAccess`, {
      method: 'GET',
    });
  },

  getPdfInstructions: (type: AccessType, ids: number[]) =>
    http<Blob, 'blob'>(endpoints[type].document, {
      method: 'POST',
      body: { ids },
      responseType: 'blob',
      parseResponse: undefined,
    }),
};
