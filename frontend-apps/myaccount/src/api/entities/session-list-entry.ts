export interface SessionListEntryEntity {
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
