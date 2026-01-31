import axios from "@/api";
import type { SessionListDTO } from "@/api/dto/session-list.ts";
import { sessionListDTOToEntity } from "@/api/mappers/session-list.ts";

export default {
    getActiveSessions: () => axios.get<SessionListDTO>("/sessions").then(({ data }) => sessionListDTOToEntity(data)),
    removeSessionById: (id: string, password: string) => axios.delete("/sessions/remove", { data: { id, password } }),
};
