import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

const schoolUnitForm = v.object({
  name: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  shortName: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  type: v.number(),
  voivodeship: v.number(),
  municipality: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  district: v.nullable(v.optional(v.pipe(v.string(), v.maxLength(255)))),
  town: v.nullable(v.optional(v.pipe(v.string(), v.maxLength(255)))),
  postalCode: v.pipe(v.string(), v.nonEmpty(), v.maxLength(7)),
  street: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  houseNumber: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  flatNumber: v.nullable(v.optional(v.pipe(v.string(), v.maxLength(255)))),
  studentCategory: v.string(),
});

export const schoolUnitFormSchema = toTypedSchema(schoolUnitForm);
export type SchoolUnitValues = v.InferOutput<typeof schoolUnitForm>;
