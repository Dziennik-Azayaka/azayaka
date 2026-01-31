export interface UserDTO {
    email: string;
    accesses: UserAccessDTO[];
}

export type UserAccessDTO = {
    id: number;
    name: string;
    type: "employee" | "student" | "guardian";
    updatedAt: string;
    modulesAvailable: ("student" | "secretary" | "administrator" | "teacher" | "secretary")[];
};
