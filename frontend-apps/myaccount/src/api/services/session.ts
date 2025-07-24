import axios from "@/api";
import type { SessionInfoDTO } from "@/api/dto/session-info.ts";
import type { SessionListDTO } from "@/api/dto/session-list.ts";
import { ApiError } from "@/api/errors.ts";
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
};
