import type { AccessStatus } from './access';

export type EmployeeAccess = {
  id: number;
  fullName: string;
  shortcut: string;
} & (
  | { status: AccessStatus.INACTIVE }
  | { status: AccessStatus.CODE_GENERATED; activationCode: string[] }
  | { status: AccessStatus.ACTIVE; activatedAt: Date; lastLoginAt: Date }
);
