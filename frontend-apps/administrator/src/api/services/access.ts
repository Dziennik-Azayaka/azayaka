import axios from "@/api";
import type { EmployeeAccessDTO } from "@/api/dto/employee-access.ts";
import { employeeAccessDTOToEntity } from "@/api/mappers/employee-access.ts";

export default {
    getEmployeeAccesses: () =>
        axios.get<EmployeeAccessDTO[]>("/employees/accesses").then((res) => res.data.map(employeeAccessDTOToEntity)),
    updateEmployeeAccesses: (ids: number[], action: "generate" | "regenerate" | "revoke") =>
        axios.patch("/employees/accesses", { ids, action }),
};
