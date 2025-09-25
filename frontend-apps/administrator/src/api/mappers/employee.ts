import type { EmployeeDTO } from "@/api/dto/employee";
import type { Employee, EmployeeRole } from "@/api/entities/employee";

export function employeeDTOToEntity(dto: EmployeeDTO): Employee {
    const roles = new Set<EmployeeRole>();
    if (dto.isAdmin) roles.add("administrator");
    if (dto.isSecretary) roles.add("secretary");
    if (dto.isTeacher) roles.add("teacher");
    if (dto.isHeadmaster) roles.add("headmaster");

    return {
        id: dto.id,
        firstName: dto.firstName,
        lastName: dto.lastName,
        shortcut: dto.shortcut,
        active: dto.active,
        roles,
    };
}
