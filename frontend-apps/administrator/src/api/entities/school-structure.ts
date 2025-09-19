export interface SchoolUnitEntity {
    id: number;
    name: string;
    typeId: number;
    studentCategory: "childrenAndYouths" | "adultsOnly";
    commune: string;
    voivodeshipId: number;
    district: string | null;
    schoolComplexId: number;
    address: string;
    shortName: string;
    archived: boolean;
}

export interface SchoolComplexEntity {
    id: number;
    name: string;
    units: SchoolUnitEntity[];
}

export type SchoolStructureEntity =
    | {
          mode: "single";
          unit: SchoolUnitEntity;
      }
    | {
          mode: "multiple";
          complex: SchoolComplexEntity;
      };
