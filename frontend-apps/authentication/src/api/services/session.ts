import { ApiError, IncorrectCredentialsError } from "../errors";
import { AxiosError } from "axios";

import axios from "@/api";

export default {
    logIn: ({ emailAddress, password }: { emailAddress: string; password: string }) =>
        axios.post("/login", { email: emailAddress, password }).catch((reason) => {
            if (reason instanceof AxiosError) {
                const errors = reason.response?.data.errors;
                if (Array.isArray(errors)) {
                    if (errors.includes("INVALID_USERNAME_OR_PASSWORD")) throw new IncorrectCredentialsError();
                }
            }
            throw new ApiError(reason);
        }),
};
