import { UAParser } from "ua-parser-js";

import type { SessionListEntryDTO } from "@/api/dto/session-list-entry.ts";
import type { SessionListEntryEntity } from "@/api/entities/session-list-entry.ts";

export const sessionListEntryDTOToEntity = (dto: SessionListEntryDTO): SessionListEntryEntity => {
    const parsedUA = UAParser(dto.userAgent);
    const deviceInfo =
        parsedUA.device.vendor && parsedUA.device.model ? `${parsedUA.device.vendor} ${parsedUA.device.model}` : null;
    const browserInfo =
        parsedUA.browser.name && parsedUA.browser.version
            ? `${parsedUA.browser.name} ${parsedUA.browser.version}`
            : null;
    const osInfo = parsedUA.os.name && parsedUA.os.version ? `${parsedUA.os.name} ${parsedUA.os.version}` : null;
    return {
        device: {
            ipAddress: dto.ipAddress,
            name: deviceInfo,
            software: browserInfo,
            os: osInfo,
            rawUserAgent: dto.userAgent,
        },
        id: dto.id,
        lastActivityDate: new Date(dto.lastActivity),
    };
};
