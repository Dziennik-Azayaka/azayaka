import type { Module } from "@azayaka-frontend/ui";

export interface UserDTO {
    email: string;
    accesses: UserAccessDTO[];
}

export type UserAccessDTO = {
    id: number;
    name: string;
    type: "employee" | "student" | "guardian";
    updatedAt: string;
    modulesAvailable: Module[];
};
