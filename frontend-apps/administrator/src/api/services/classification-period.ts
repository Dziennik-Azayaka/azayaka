import type { ClassificationPeriodDTO } from "../dto/classification-period";
import { classificationPeriodDTOToEntity } from "../mappers/classification-period";

import axios from "@/api";

export default {
    getClassificationPeriodsByUnitId: (unitId: number, schoolYear: number) =>
        axios
            .get<ClassificationPeriodDTO[]>(`/schoolUnits/${unitId}/classificationPeriods/${schoolYear}`)
            .then(({ data }) => data.map(classificationPeriodDTOToEntity)),
};
