import axios from "@/api";
import type { SessionInfoDTO } from "@/api/dto/session-info.ts";
import { ApiError } from "@/api/errors.ts";
import { sessionInfoDTOToEntity } from "@/api/mappers/session-info.ts";

export default {
    getSessionInfo: () =>
        axios
            .get<SessionInfoDTO>("/session")
            .then(({ data }) => sessionInfoDTOToEntity(data))
            .catch((reason) => {
                throw new ApiError(reason);
            }),
};
