import type { SessionListEntryDTO } from "@/api/dto/session-list-entry.ts";

export interface SessionListDTO {
    currentSession: string;
    sessions: SessionListEntryDTO[];
}
