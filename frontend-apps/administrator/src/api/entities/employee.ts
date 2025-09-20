export type EmployeeRole = "teacher" | "administrator" | "secretary" | "headmaster";

export interface Employee {
    id: number;
    firstName: string;
    lastName: string;
    shortcut: string;
    active: boolean;
    roles: Set<EmployeeRole>;
}
