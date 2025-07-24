export interface ActivityLogEntryEntity {
    type:
        | "failed_login_attempt"
        | "successful_login_attempt"
        | "logout"
        | "credentials_changed"
        | "logged_out_by_another_device";
    date: Date;
    device: {
        name: string | null;
        os: string | null;
        software: string | null;
        rawUserAgent: string;
        ipAddress: string;
    };
}
