import type { SessionListDTO } from "@/api/dto/session-list.ts";
import type { SessionListEntity } from "@/api/entities/session-list.ts";
import { sessionListEntryDTOToEntity } from "@/api/mappers/session-list-entry.ts";

export const sessionListDTOToEntity = (dto: SessionListDTO): SessionListEntity => ({
    currentSessionId: dto.currentSession,
    sessions: dto.sessions.map(sessionListEntryDTOToEntity),
});
