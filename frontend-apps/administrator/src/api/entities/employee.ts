export type EmployeeRole = "teacher" | "administrator" | "secretary" | "headmaster";

export interface EmployeeEntity {
    id: number;
    firstName: string;
    lastName: string;
    shortcut: string;
    active: boolean;
    roles: Set<EmployeeRole>;
}
