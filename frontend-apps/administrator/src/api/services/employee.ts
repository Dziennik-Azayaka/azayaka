import axios from "@/api";
import type { Employee } from "@/api/entities/employee.ts";

export default {
    getAllEmployees: () => axios.get<Employee[]>("/employees").then((res) => res.data),
};
