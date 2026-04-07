import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

const schoolUnitForm = v.object({
  name: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  shortName: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  type: v.number(),
  address: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  voivodeship: v.number(),
  municipality: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  district: v.nullable(v.optional(v.pipe(v.string(), v.maxLength(255)))),
  studentCategory: v.string(),
});

export const schoolUnitFormSchema = toTypedSchema(schoolUnitForm);
export type SchoolUnitValues = v.InferOutput<typeof schoolUnitForm>;
