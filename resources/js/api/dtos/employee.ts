import type { Employee, EmployeeRole } from '../types/employee';

export interface EmployeeDTO {
  id: number;
  firstName: string;
  lastName: string;
  shortcut: string;
  active: boolean;
  isAdmin: boolean;
  isHeadmaster: boolean;
  isSecretary: boolean;
  isTeacher: boolean;
}

export interface EmployeeBodyDTO {
  lastName: string;
  firstName: string;
  shortcut: string | null;
  isAdmin: boolean;
  isHeadmaster: boolean;
  isSecretary: boolean;
  isTeacher: boolean;
}

export function employeeFromDTO(dto: EmployeeDTO): Employee {
  const roles = new Set<EmployeeRole>();
  if (dto.isAdmin) roles.add('administrator');
  if (dto.isSecretary) roles.add('secretary');
  if (dto.isTeacher) roles.add('teacher');
  if (dto.isHeadmaster) roles.add('headmaster');

  return {
    id: dto.id,
    firstName: dto.firstName,
    lastName: dto.lastName,
    shortcut: dto.shortcut,
    active: dto.active,
    roles,
  };
}
