import { CalendarDate } from '@internationalized/date';
import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

const classificationPeriodsForm = v.object({
  periodsNumber: v.pipe(v.number(), v.minValue(2), v.maxValue(4)),
  periods: v.array(v.object({ start: v.instance(CalendarDate) })),
});

export const classificationPeriodsFormSchema = toTypedSchema(classificationPeriodsForm);
export type ClassificationPeriodsValues = v.InferOutput<typeof classificationPeriodsForm>;
