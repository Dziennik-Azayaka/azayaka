export interface Class {
  id: number;
  schoolUnit: {
    id: number;
    name: string;
    shortName: string;
  };
  promoteEvery: 'year' | 'semester';
  alias: string;
  mark: string;
  startingClassificationPeriodId: number;
  startingClassificationPeriodYear: number;
  startingClassificationPeriodNumber: number;
  teachingCycleLength: number;
  level: number | null;
  formTutors: FormTutor[];
}

export interface FormTutor {
  employeeId: number;
  firstName: string;
  lastName: string;
  dateFrom: Date;
  dateTo: Date;
}
