import axios from "@/api";
import type { ClassDTO } from "@/api/dto/class.ts";
import type { ClassEntity } from "@/api/entities/class.ts";
import type { ClassGeneralInfoForm } from "@/types";

export default {
    getAllClasses: (category?: "current" | "future" | "archive") =>
        axios
            .get<ClassDTO[]>(`/schoolUnits/all/classUnits?category=${category}`)
            .then(({ data }): ClassEntity[] => data),
    addClass: (generalInfo: ClassGeneralInfoForm, teacherIds: number[]) =>
        axios.post(`/schoolUnits/${generalInfo.schoolUnitId}/classUnits`, {
            ...generalInfo,
            startingClassificationPeriodId: generalInfo.startingClassificationPeriod.id,
            employeeIds: teacherIds,
        }),
};
