export interface SchoolUnitForm {
    name: string;
    typeId: number;
    address: string;
    voivodeshipId: number;
    commune: string;
    district: string | null;
    studentCategory: string;
    shortName: string;
}

export interface SchoolComplexForm {
    name: string;
}
