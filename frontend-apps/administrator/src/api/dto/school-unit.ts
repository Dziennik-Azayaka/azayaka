export interface SchoolUnitDTO {
    id: number;
    name: string;
    type: number;
    studentCategory: "childrenAndYouths" | "adultsOnly";
    municipality: string;
    voivodeship: number;
    district: string | null;
    schoolComplexId: number;
    address: string;
    shortName: string;
    active: boolean;
}
