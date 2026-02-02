import type { ComposerTranslation } from "vue-i18n";
import z from "zod";

export const optionalStringField = (t: ComposerTranslation, options?: { maxLength?: number }) => {
    const maxLength = options?.maxLength ?? 255;

    return z
        .string()
        .max(maxLength, t("fieldMaxLengthError", { number: maxLength }))
        .optional()
        .nullable();
};

export const requiredStringField = (t: ComposerTranslation, options?: { maxLength?: number; minLength?: number }) => {
    const maxLength = options?.maxLength ?? 255;
    const minLength = options?.minLength ?? 1;

    return z
        .string({
            required_error: t("requiredFieldError"),
        })
        .min(minLength, {
            message: t(minLength === 1 ? "requiredFieldError" : "fieldMinLengthError", { number: minLength }),
        })
        .max(maxLength, t("fieldMaxLengthError", { number: maxLength }));
};
