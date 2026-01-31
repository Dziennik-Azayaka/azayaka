import type { AxiosError } from "axios";

export class ApiError {
    constructor(
        public code: string,
        public httpStatus: number | null,
    ) {}

    static fromAxiosError(error: AxiosError<{ errors: string[] }>) {
        const code = error.response?.data?.errors?.[0] ?? "UNKNOWN_ERROR";
        const httpStatus = error?.status ?? null;
        return new ApiError(code, httpStatus);
    }

    public getTranslationId() {
        switch (this.code) {
            case "NAME_TAKEN":
                return "takenNameError";
            case "SHORTCUT_TAKEN":
                return "takenShortcutError";
            case "WRONG_PASSWORD":
                return "incorrectPasswordError";
            default:
                return "unknownError";
        }
    }
}
