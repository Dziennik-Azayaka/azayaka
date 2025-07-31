import { AxiosError } from "axios";

import axios from "@/api";
import type { SessionInfoDTO } from "@/api/dto/session-info.ts";
import type { SessionListDTO } from "@/api/dto/session-list.ts";
import { ApiError, IncorrectPasswordError } from "@/api/errors.ts";
import { sessionInfoDTOToEntity } from "@/api/mappers/session-info.ts";
import { sessionListDTOToEntity } from "@/api/mappers/session-list.ts";

export default {
    getActiveSession: () =>
        axios
            .get<SessionListDTO>("/sessions")
            .then(({ data }) => sessionListDTOToEntity(data))
            .catch((reason) => {
                throw new ApiError(reason);
            }),
    getSessionInfo: () =>
        axios
            .get<SessionInfoDTO>("/session")
            .then(({ data }) => sessionInfoDTOToEntity(data))
            .catch((reason) => {
                throw new ApiError(reason);
            }),
    removeSessionById: (id: string, password: string) =>
        axios.delete("/sessions/remove", { data: { id, password } }).catch((reason) => {
            if (reason instanceof AxiosError) {
                const errors = reason.response?.data.errors;
                if (Array.isArray(errors)) {
                    if (errors.includes("WRONG_PASSWORD")) throw new IncorrectPasswordError();
                }
            }
            throw new ApiError(reason);
        }),
};
