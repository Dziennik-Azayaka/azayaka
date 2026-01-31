export interface UserEntity {
    emailAddress: string;
    accesses: UserAccessEntity[];
}

export type UserAccessEntity = {
    type: "employee" | "student" | "guardian";
    id: number;
    name: string;
    modules: ("student" | "secretary" | "administrator" | "teacher" | "secretary")[];
};
