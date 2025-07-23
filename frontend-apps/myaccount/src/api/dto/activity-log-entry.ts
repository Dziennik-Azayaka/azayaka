export interface ActivityLogEntryDTO {
    eventType:
        | "failed_login_attempt"
        | "successful_login_attempt"
        | "logout"
        | "credentials_changed"
        | "logged_out_by_another_device";
    ip: string;
    userAgent: string;
    createdAt: string;
}
