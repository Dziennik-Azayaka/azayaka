import { AxiosError } from "axios";

import axios from "@/api";
import type { SchoolComplexDTO } from "@/api/dto/school-complex";
import type { SchoolUnitDTO } from "@/api/dto/school-unit";
import { ApiError, IncorrectPasswordError } from "@/api/errors";
import { schoolStructureDTOsToEntity } from "@/api/mappers/school-structure";
import type { SchoolComplexForm, SchoolUnitForm } from "@/types";

export default {
    getStructure: async () => {
        try {
            const [{ data: complexes }, { data: units }] = await Promise.all([
                axios.get<SchoolComplexDTO[]>("/schoolComplex"),
                axios.get<SchoolUnitDTO[]>("/schoolUnits"),
            ]);
            return schoolStructureDTOsToEntity(complexes.length ? complexes[0] : null, units);
        } catch (reason) {
            throw new ApiError(reason as string);
        }
    },
    createSchoolUnit: (unit: SchoolUnitForm, schoolComplexId: number) =>
        axios.post("/schoolUnits", {
            name: unit.name,
            type: unit.typeId,
            studentCategory: unit.studentCategory,
            municipality: unit.commune,
            voivodeship: unit.voivodeshipId,
            district: unit.district ?? null,
            schoolComplexId,
            address: unit.address,
            shortName: unit.shortName,
        }),
    editSchoolUnitData: (unit: SchoolUnitForm, schoolUnitId: number, schoolComplexId: number) =>
        axios.put(`/schoolUnits/${schoolUnitId}`, {
            name: unit.name,
            type: unit.typeId,
            studentCategory: unit.studentCategory,
            municipality: unit.commune,
            voivodeship: unit.voivodeshipId,
            district: unit.district ?? null,
            schoolComplexId,
            address: unit.address,
            shortName: unit.shortName,
        }),
    createSchoolComplex: (complex: SchoolComplexForm) =>
        axios.post("/schoolComplex", {
            name: complex.name,
        }),
    editSchoolComplexData: (complex: SchoolComplexForm, id: number) =>
        axios.put(`/schoolComplex/${id}`, {
            name: complex.name,
            type: 90,
        }),
    changeUnitActivity: (state: boolean, id: number, password: string) =>
        axios.put(`/schoolUnits/${id}/activity`, { state, password }).catch((reason) => {
            if (reason instanceof AxiosError) {
                const errors = reason.response?.data.errors;
                if (Array.isArray(errors)) {
                    if (errors.includes("WRONG_PASSWORD")) throw new IncorrectPasswordError();
                }
            }
            throw new ApiError(reason);
        }),
};
