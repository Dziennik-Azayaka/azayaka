import type { UserDTO } from "../dto/user";
import type { UserEntity } from "../entities/user";

export const userDTOToEntity = (dto: UserDTO): UserEntity => ({
    emailAddress: dto.email,
    accesses: dto.accesses
        .map((access) => ({
            id: access.id,
            name: access.name,
            type: access.type,
            modules: access.modulesAvailable,
        }))
        .filter((it) => !!it),
});
