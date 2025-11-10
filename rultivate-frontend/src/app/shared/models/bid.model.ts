export interface Bid {
  id: number;
  rfqId: number;
  vendorId: number;
  amountInr: number;
  deliveryTimelineDays: number;
  notes?: string;
  status: 'SUBMITTED' | 'UPDATED' | 'WITHDRAWN' | 'ACCEPTED' | 'REJECTED';
  createdAt: string;
  updatedAt: string;
}
