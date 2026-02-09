import axios from "@/api";
import type { ClassDTO } from "@/api/dto/class.ts";
import type { ClassEntity } from "@/api/entities/class.ts";

export default {
    getAllClasses: (category?: "current" | "future" | "archive") =>
        axios
            .get<ClassDTO[]>(`/schoolUnits/all/classUnits?category=${category}`)
            .then(({ data }): ClassEntity[] => data),
};
