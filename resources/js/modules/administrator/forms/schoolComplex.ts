import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

const schoolComplexForm = v.object({
  name: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
});

export const schoolComplexFormSchema = toTypedSchema(schoolComplexForm);
export type SchoolComplexValues = v.InferOutput<typeof schoolComplexForm>;
