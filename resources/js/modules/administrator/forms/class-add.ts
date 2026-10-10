import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

const classAddForm = v.object({
  alias: v.optional(v.pipe(v.string(), v.maxLength(255))),
  mark: v.pipe(v.string(), v.nonEmpty(), v.maxLength(3)),
  startingClassificationPeriodId: v.object({
    id: v.number(),
    schoolYear: v.number(),
    number: v.number()
  }),
  teachingCycleLength: v.pipe(v.number(), v.minValue(2), v.maxValue(8)),
  promoteEvery: v.string(),
  schoolUnitId: v.number(),
  formTutors: v.array(v.number())
});

export const classAddFormSchema = toTypedSchema(classAddForm);
export type ClassAddValues = v.InferOutput<typeof classAddForm>;
