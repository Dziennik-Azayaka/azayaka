import axios from "@/api";
import { ApiError } from "@/api/errors.ts";
import { sessionListDTOToEntity } from "@/api/mappers/session-list.ts";
import type { SessionListDTO } from "@/api/dto/session-list.ts";

export default {
    getActiveSession: () =>
        axios
            .get<SessionListDTO>("/sessions")
            .then(({ data }) => sessionListDTOToEntity(data))
            .catch((reason) => {
                throw new ApiError(reason);
            }),
};
