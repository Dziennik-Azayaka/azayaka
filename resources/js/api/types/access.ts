export enum AccessStatus {
  INACTIVE = 'inactive',
  CODE_GENERATED = 'codeGenerated',
  ACTIVE = 'active',
}

export type AccessType = 'employee' | 'student' | 'guardian';

export type AccessAction = 'generate' | 'regenerate' | 'revoke';

export interface AccessEntry {
  rowKey: string;
  entityId: number;
  type: AccessType;
  fullName: string;
  secondary?: string;
  studentId?: number;
  studentName?: string;
  status: AccessStatus;
  activationCode?: string[];
  activatedAt?: Date;
  lastLoginAt?: Date;
}
