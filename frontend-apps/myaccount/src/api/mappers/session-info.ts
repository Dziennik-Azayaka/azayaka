import type { SessionInfoDTO } from "@/api/dto/session-info.ts";
import type { SessionInfoEntity } from "@/api/entities/session-info.ts";

export const sessionInfoDTOToEntity = (dto: SessionInfoDTO): SessionInfoEntity =>
    dto.loggedIn
        ? {
              loggedIn: true,
              emailAddress: dto.email,
          }
        : { loggedIn: false };
