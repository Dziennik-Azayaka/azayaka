import type { ClassificationPeriodDTO } from "../dto/classification-period";
import type { ClassificationPeriodEntity } from "../entities/classification-period";

export const classificationPeriodDTOToEntity = (dto: ClassificationPeriodDTO): ClassificationPeriodEntity => ({
    ...dto,
    start: new Date(dto.periodStart),
    end: new Date(dto.periodEnd),
    number: dto.periodNumber,
});
