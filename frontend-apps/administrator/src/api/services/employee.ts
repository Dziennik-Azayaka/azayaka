import { AxiosError } from "axios";



import axios from "@/api";
import type { EmployeeDTO } from "@/api/dto/employee.ts";
import { ApiError, IncorrectPasswordError, TakenShortcutError } from "@/api/errors.ts";
import { employeeDTOToEntity } from "@/api/mappers/employee";
import type { EmployeeForm } from "@/types.ts";

export default {
    getAllEmployees: () => axios.get<EmployeeDTO[]>("/employees").then((res) => res.data.map(employeeDTOToEntity)),
    addEmployee: async (employee: EmployeeForm) =>
        axios.post("/employees", {
            firstName: employee.firstName,
            lastName: employee.lastName,
            shortcut: employee.shortcut,
            isAdmin: employee.roles.includes("administrator"),
            isHeadmaster: employee.roles.includes("headmaster"),
            isSecretary: employee.roles.includes("secretary"),
            isTeacher: employee.roles.includes("teacher"),
        }).catch((reason) => {
            if (reason instanceof AxiosError) {
                const errors = reason.response?.data.errors;
                if (Array.isArray(errors) && errors.includes("SHORTCUT_TAKEN")) throw new TakenShortcutError();
            }
            throw new ApiError(reason);
        }),
    changeEmployeeActivity: (state: boolean, id: number, password: string) =>
        axios.put(`/employees/${id}/activity`, { state, password }).catch((reason) => {
            if (reason instanceof AxiosError) {
                const errors = reason.response?.data.errors;
                if (Array.isArray(errors) && errors.includes("WRONG_PASSWORD")) throw new IncorrectPasswordError();
            }
            throw new ApiError(reason);
        }),
    editEmployeeData: async (id: number, employee: EmployeeForm) =>
        axios.put(`/employees/${id}`, {
            firstName: employee.firstName,
            lastName: employee.lastName,
            shortcut: employee.shortcut,
            isAdmin: employee.roles.includes("administrator"),
            isHeadmaster: employee.roles.includes("headmaster"),
            isSecretary: employee.roles.includes("secretary"),
            isTeacher: employee.roles.includes("teacher"),
        }).catch((reason) => {
            if (reason instanceof AxiosError) {
                const errors = reason.response?.data.errors;
                if (Array.isArray(errors) && errors.includes("SHORTCUT_TAKEN")) throw new TakenShortcutError();
            }
            throw new ApiError(reason);
        }),
};
