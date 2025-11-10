export interface Rfq {
  id: number;
  customerId: number;
  title: string;
  description: string;
  category: string;
  budgetInr?: number;
  status: 'DRAFT' | 'PUBLISHED' | 'CLOSED';
  expectedDeliveryDate?: string;
  createdAt: string;
  updatedAt: string;
}

export interface RfqInvite {
  id: number;
  rfqId: number;
  vendorId: number;
  status: 'INVITED' | 'ACCEPTED' | 'DECLINED';
}
