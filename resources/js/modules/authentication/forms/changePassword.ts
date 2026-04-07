import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

export const changePasswordSchema = toTypedSchema(
  v.pipe(
    v.object({
      oldPassword: v.pipe(v.string(), v.nonEmpty()),
      newPassword: v.pipe(v.string(), v.minLength(8), v.maxLength(255)),
      repeatPassword: v.string(),
    }),
    v.check(
      (input) => input.newPassword === input.repeatPassword,
      'validationErrors.passwordsMustMatch',
    ),
    v.forward(
      v.check(
        (input) => input.newPassword === input.repeatPassword,
        'validationErrors.passwordsMustMatch',
      ),
      ['repeatPassword'] as const,
    ),
  ),
);
