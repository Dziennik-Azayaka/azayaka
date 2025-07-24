import type { SessionListEntryEntity } from "@/api/entities/session-list-entry.ts";

export interface SessionListEntity {
    currentSessionId: string;
    sessions: SessionListEntryEntity[];
}
