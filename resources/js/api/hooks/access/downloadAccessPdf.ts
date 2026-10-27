import { AccessService } from '../../services/access';
import type { AccessType } from '@/api/types/access';
import { useMutation } from '@tanstack/vue-query';

export const useDownloadAccessPdf = () =>
  useMutation({
    mutationKey: ['downloadAccessPdfInstructions'],
    mutationFn: ({ type, ids }: { type: AccessType; ids: number[] }) =>
      AccessService.getPdfInstructions(type, ids),
  });
