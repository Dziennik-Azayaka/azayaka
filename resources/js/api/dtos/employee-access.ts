import { AccessStatus } from '../types/access';
import type { EmployeeAccess } from '../types/employee-access';

export interface EmployeeAccessDTO {
  id: number;
  shortcut: string;
  firstName: string;
  lastName: string;
  accessCreated: boolean;
  accessWords: string | null;
  activationDate: string | null;
  lastLoginDate: null;
}

function detectAccessStatus(dto: EmployeeAccessDTO) {
  if (dto.activationDate) return AccessStatus.ACTIVE;
  if (dto.accessWords) return AccessStatus.CODE_GENERATED;
  return AccessStatus.INACTIVE;
}

export function employeeAccessFromDTO(dto: EmployeeAccessDTO): EmployeeAccess {
  const status = detectAccessStatus(dto);

  const common = {
    id: dto.id,
    fullName: `${dto.lastName} ${dto.firstName}`,
    shortcut: dto.shortcut,
    status: status,
  };

  if (common.status === AccessStatus.ACTIVE)
    return {
      ...common,
      activatedAt: new Date(dto.activationDate!),
      lastLoginAt: new Date(dto.lastLoginDate!),
    } as unknown as EmployeeAccess;

  if (common.status === AccessStatus.CODE_GENERATED)
    return {
      ...common,
      activationCode: dto.accessWords!.split(','),
    } as unknown as EmployeeAccess;

  return common as unknown as EmployeeAccess;
}
