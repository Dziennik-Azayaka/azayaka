import type { SchoolComplexDTO } from "../dto/school-complex";
import type { SchoolUnitDTO } from "../dto/school-unit";
import type { SchoolComplexEntity, SchoolStructureEntity, SchoolUnitEntity } from "../entities/school-structure";

const schoolUnitDTOToEntity = (dto: SchoolUnitDTO): SchoolUnitEntity => ({
    id: dto.id,
    name: dto.name,
    typeId: dto.type,
    studentCategory: dto.studentCategory,
    commune: dto.municipality,
    voivodeshipId: dto.voivodeship,
    district: dto.district,
    schoolComplexId: dto.schoolComplexId,
    address: dto.address,
    shortName: dto.shortName,
    archived: !dto.active,
});

const schoolComplexDTOToEntity = (dto: SchoolComplexDTO, unitDTOs: SchoolUnitDTO[]): SchoolComplexEntity => ({
    id: dto.id,
    name: dto.name,
    units: unitDTOs.map(schoolUnitDTOToEntity),
});

export function schoolStructureDTOsToEntity(
    complex: SchoolComplexDTO | null,
    units: SchoolUnitDTO[],
): SchoolStructureEntity {
    if (complex !== null) {
        return {
            mode: "multiple",
            complex: schoolComplexDTOToEntity(complex, units),
        };
    }
    return {
        mode: "single",
        unit: schoolUnitDTOToEntity(units[0]),
    };
}
