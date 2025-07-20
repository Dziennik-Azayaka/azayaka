import { AxiosError } from "axios";

import axios from "@/api";
import type { ActivationCodeInfoDTO } from "@/api/dto/activation-code-info";
import type { ActivationStatus } from "@/api/dto/activation-status";
import { ApiError, IncorrectActivationCodeError, IncorrectCredentialsError } from "@/api/errors";

export default {
    checkCode: (words: string[]) =>
        axios
            .post<ActivationCodeInfoDTO>("/activation/lookup", { code: words.join(",") })
            .then(({ data }) => data)
            .catch((reason) => {
                if (reason instanceof AxiosError) {
                    const errors = reason.response?.data.errors;
                    if (Array.isArray(errors)) {
                        if (errors.includes("CODE_NOT_FOUND")) throw new IncorrectActivationCodeError();
                    }
                }
                throw new ApiError(reason);
            }),
    checkEmailAddress: (emailAddress: string) =>
        axios
            .post<{ available: boolean }>("/activation/emailAvailability", { email: emailAddress })
            .then(({ data }) => ({ accountExist: !data.available }))
            .catch((reason) => {
                throw new ApiError(reason);
            }),
    getStatus: () =>
        axios
            .get<ActivationStatus>("/activation/status")
            .then(({ data }) => data)
            .catch((reason) => {
                throw new ApiError(reason);
            }),
    activate: (code: string[], emailAddress: string, password: string) =>
        axios
            .post("/activation", {
                code: code.join(","),
                email: emailAddress,
                password,
            })
            .catch((reason) => {
                if (reason instanceof AxiosError) {
                    const errors = reason.response?.data.errors;
                    if (Array.isArray(errors)) {
                        if (errors.includes("ACTIVATION_CODE_NOT_FOUND")) throw new IncorrectActivationCodeError();
                        if (errors.includes("WRONG_PASSWORD")) throw new IncorrectCredentialsError();
                    }
                }
                throw new ApiError(reason);
            }),
};
