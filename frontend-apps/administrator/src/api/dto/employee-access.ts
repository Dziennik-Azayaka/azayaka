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
