import type { AccessStatus } from "@/api/entities/access.ts";

export type EmployeeAccessEntity = {
    id: number;
    fullName: string;
    shortcut: string;
} & (
    | { status: AccessStatus.INACTIVE }
    | { status: AccessStatus.CODE_GENERATED; activationCode: string[] }
    | { status: AccessStatus.ACTIVE; activatedAt: Date; lastLoginAt: Date }
);
