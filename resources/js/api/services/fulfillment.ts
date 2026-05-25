import { http } from '@/config/ofetch';

export interface FulfillmentBody {
  schoolYear: number;
  controlDate: string;
  kindergartenInfo?: string | null;
  postponementInfo?: string | null;
  schoolInfo?: string | null;
  outOfSchoolInfo?: string | null;
  level: number;
}

export const FulfillmentService = {
  create: (childId: number, data: FulfillmentBody): Promise<{ success: true }> =>
    http(`/children/${childId}/fulfillment`, { method: 'POST', body: data }),

  update: (childId: number, fulfillmentId: number, data: FulfillmentBody): Promise<{ success: true }> =>
    http(`/children/${childId}/fulfillment/${fulfillmentId}`, { method: 'PUT', body: data }),

  delete: (childId: number, fulfillmentId: number): Promise<{ success: true }> =>
    http(`/children/${childId}/fulfillment/${fulfillmentId}`, { method: 'DELETE' }),
};
