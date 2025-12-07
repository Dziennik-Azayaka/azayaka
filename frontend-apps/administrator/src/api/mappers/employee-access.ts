import type { EmployeeAccessDTO } from "@/api/dto/employee-access";
import { AccessStatus } from "@/api/entities/access.ts";
import type { EmployeeAccessEntity } from "@/api/entities/employee-access";

function detectAccessStatus(dto: EmployeeAccessDTO) {
    if (dto.activationDate) return AccessStatus.ACTIVE;
    if (dto.accessWords) return AccessStatus.CODE_GENERATED;
    return AccessStatus.UNACTIVE;
}

export function employeeAccessDTOToEntity(dto: EmployeeAccessDTO): EmployeeAccessEntity {
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
        } as unknown as EmployeeAccessEntity;

    if (common.status === AccessStatus.CODE_GENERATED)
        return {
            ...common,
            activationCode: dto.accessWords!.split(","),
        } as unknown as EmployeeAccessEntity;

    return common as unknown as EmployeeAccessEntity;
}
