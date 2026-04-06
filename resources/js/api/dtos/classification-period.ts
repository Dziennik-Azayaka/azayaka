import type { ClassificationPeriod } from '../types/classification-period';

export interface ClassificationPeriodDTO {
  id: number;
  periodEnd: string;
  periodNumber: number;
  periodStart: string;
  schoolYear: number;
}

export const classificationPeriodFromDTO = (
  dto: ClassificationPeriodDTO,
): ClassificationPeriod => ({
  ...dto,
  start: new Date(dto.periodStart),
  end: new Date(dto.periodEnd),
  number: dto.periodNumber,
});
