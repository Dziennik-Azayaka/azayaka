export interface SchoolUnit {
  id: number;
  name: string;
  type: number;
  studentCategory: 'childrenAndYouths' | 'adultsOnly';
  municipality: string;
  voivodeship: number;
  district: string | null;
  schoolComplexId: number;
  address: string;
  shortName: string;
  active: boolean;
}

export interface SchoolComplex {
  id: number;
  name: string;
  units: SchoolUnit[];
}

export type SchoolStructure =
  | {
      mode: 'single';
      unit: SchoolUnit;
    }
  | {
      mode: 'multiple';
      complex: SchoolComplex;
    };
