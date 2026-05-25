export interface ClassUnit {
  id: number;
  alias: string | null;
  mark: string;
  startingClassificationPeriodId: number;
  startingClassificationPeriodYear: number;
  startingClassificationPeriodNumber: number;
  teachingCycleLength: number;
  level: number | null;
  promoteEvery: 'year' | 'semester';
  formTutors: FormTutor[];
  schoolUnit: {
    id: number;
    name: string;
    shortName: string;
  };
}

export interface FormTutor {
  employeeId: number;
  firstName: string;
  lastName: string;
  dateFrom: string;
  dateTo: string;
}
