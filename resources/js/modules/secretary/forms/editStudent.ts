import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

const editStudentForm = v.object({
  firstName: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  lastName: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  secondName: v.optional(v.pipe(v.string(), v.maxLength(255))),
  pesel: v.optional(v.pipe(v.string(), v.maxLength(11))),
  alternateIdentityDocument: v.optional(v.pipe(v.string(), v.maxLength(255))),
  birthdate: v.pipe(v.string(), v.nonEmpty()),
  birthplace: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  gender: v.optional(v.union([v.literal('male'), v.literal('female')])),
  residenceAddressCountry: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  residenceAddressCommune: v.optional(v.pipe(v.string(), v.maxLength(255))),
  residenceAddressTown: v.optional(v.pipe(v.string(), v.maxLength(255))),
  residenceAddressPostalCode: v.optional(v.pipe(v.string(), v.maxLength(255))),
  residenceAddressStreet: v.optional(v.pipe(v.string(), v.maxLength(255))),
  residenceAddressHouseNumber: v.optional(v.pipe(v.string(), v.maxLength(255))),
  residenceAddressFlatNumber: v.optional(v.pipe(v.string(), v.maxLength(255))),
  admissionDate: v.pipe(v.string(), v.nonEmpty()),
  leaveDate: v.optional(v.pipe(v.string(), v.maxLength(255))),
  leaveReason: v.optional(v.pipe(v.string(), v.maxLength(255))),
});

export const editStudentFormSchema = toTypedSchema(editStudentForm);
export type EditStudentFormValues = v.InferOutput<typeof editStudentForm>;
