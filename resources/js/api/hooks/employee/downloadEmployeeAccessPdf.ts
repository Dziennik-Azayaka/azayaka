import { EmployeeService } from '../../services/employee';
import { useMutation } from '@tanstack/vue-query';

export const useDownloadEmployeeAccessPdf = () =>
  useMutation({
    mutationKey: ['downloadAccessPdfInstruction'],
    mutationFn: (ids: number[]) => EmployeeService.getPdfInstructions(ids),
  });
