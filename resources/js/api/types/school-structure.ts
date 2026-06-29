export interface SchoolUnit {
  id: number;
  name: string;
  type: number;
  studentCategory: 'childrenAndYouths' | 'adultsOnly';
  municipality: string;
  voivodeship: number;
  town: string;
  district: string | null;
  postalCode: string;
  post: string;
  street: string | null;
  houseNumber: string;
  flatNumber: string | null;
  schoolComplexId: number;
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
