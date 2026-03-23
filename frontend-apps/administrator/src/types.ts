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

export interface EmployeeForm {
    lastName: string;
    firstName: string;
    shortcut: string;
    roles: string[];
}

export interface SubjectForm {
    name: string;
    shortcut: string;
}

export interface ClassGeneralInfoForm {
    schoolUnitId: number;
    mark: string;
    alias: string | null;
    promoteEvery: "year" | "semester";
    teachingCycleLength: number;
    startingClassificationPeriod: {
        id: number;
        number: number;
        schoolYear: number;
    };
}
