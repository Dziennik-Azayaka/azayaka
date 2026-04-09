export interface SessionList {
  currentSession: string;
  sessions: SessionListEntry[];
}

export interface SessionListEntry {
  id: string;
  device: {
    name: string | null;
    os: string | null;
    software: string | null;
    rawUserAgent: string;
    ipAddress: string;
  };
  lastActivityDate: Date;
}
