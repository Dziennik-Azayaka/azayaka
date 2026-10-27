import type { AccessEntry } from '../types/access';
import { AccessStatus } from '../types/access';

export interface AccountAccessDTO {
  id: number;
  words: string | null;
  studentId: number | null;
  guardianId: number | null;
  employeeId: number | null;
  userId: number | null;
  createdAt: string;
  activatedAt: string | null;
}

export interface EmployeeAccessDTO {
  id: number;
  shortcut: string;
  firstName: string;
  lastName: string;
  accessCreated: boolean;
  accessWords: string | null;
  activationDate: string | null;
  lastLoginDate: string | null;
}

export interface StudentAccessDTO {
  firstName: string;
  secondName: string | null;
  lastName: string;
  studentId: number;
  accessWords: AccountAccessDTO | null;
}

export interface GuardianStudentDTO {
  studentId: number;
  firstName: string;
  secondName: string | null;
  lastName: string;
}

export interface GuardianAccessDTO {
  id: number;
  firstName: string;
  secondName: string | null;
  lastName: string;
  students: GuardianStudentDTO[];
  accessWords: AccountAccessDTO | null;
}

function formatName(firstName: string, secondName: string | null, lastName: string) {
  return [lastName, firstName, secondName].filter(Boolean).join(' ');
}

function accessDetailsFromDTO(access: AccountAccessDTO | null) {
  const status = !access
    ? AccessStatus.INACTIVE
    : access.userId
      ? AccessStatus.ACTIVE
      : access.words
        ? AccessStatus.CODE_GENERATED
        : AccessStatus.INACTIVE;

  return {
    status,
    activationCode:
      status === AccessStatus.CODE_GENERATED && access?.words ? access.words.split(',') : undefined,
    activatedAt:
      status === AccessStatus.ACTIVE
        ? new Date(access?.activatedAt ?? access?.createdAt ?? '')
        : undefined,
  };
}

export function employeeAccessFromDTO(dto: EmployeeAccessDTO): AccessEntry {
  const status = dto.activationDate
    ? AccessStatus.ACTIVE
    : dto.accessWords
      ? AccessStatus.CODE_GENERATED
      : AccessStatus.INACTIVE;

  return {
    rowKey: `employee-${dto.id}`,
    entityId: dto.id,
    type: 'employee',
    fullName: `${dto.lastName} ${dto.firstName}`,
    secondary: dto.shortcut,
    status,
    activationCode:
      status === AccessStatus.CODE_GENERATED && dto.accessWords
        ? dto.accessWords.split(',')
        : undefined,
    activatedAt: dto.activationDate ? new Date(dto.activationDate) : undefined,
    lastLoginAt: dto.lastLoginDate ? new Date(dto.lastLoginDate) : undefined,
  };
}

export function studentAccessFromDTO(dto: StudentAccessDTO): AccessEntry {
  return {
    rowKey: `student-${dto.studentId}`,
    entityId: dto.studentId,
    type: 'student',
    fullName: formatName(dto.firstName, dto.secondName, dto.lastName),
    ...accessDetailsFromDTO(dto.accessWords),
  };
}

export function guardianAccessFromDTO(dto: GuardianAccessDTO): AccessEntry {
  const student = dto.students[0];

  return {
    rowKey: `guardian-${dto.id}`,
    entityId: dto.id,
    studentId: student?.studentId,
    type: 'guardian',
    fullName: formatName(dto.firstName, dto.secondName, dto.lastName),
    studentName: student
      ? formatName(student.firstName, student.secondName, student.lastName)
      : undefined,
    ...accessDetailsFromDTO(dto.accessWords),
  };
}
