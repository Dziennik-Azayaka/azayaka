import axios from "@/api";
import type { EmployeeDTO } from "@/api/dto/employee.ts";
import { employeeDTOToEntity } from "@/api/mappers/employee";

export default {
    getAllEmployees: () => axios.get<EmployeeDTO[]>("/employees").then((res) => res.data.map(employeeDTOToEntity)),
};
