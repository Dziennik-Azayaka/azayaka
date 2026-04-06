import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

const employeeForm = v.object({
  firstName: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  lastName: v.pipe(v.string(), v.nonEmpty(), v.maxLength(255)),
  shortcut: v.optional(v.pipe(v.string(), v.maxLength(4))),
  roles: v.pipe(v.array(v.string()), v.nonEmpty()),
});

export const employeeFormSchema = toTypedSchema(employeeForm);
export type EmployeeValues = v.InferOutput<typeof employeeForm>;
