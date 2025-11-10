export interface PaymentRecord {
  id: number;
  orderId?: number;
  vendorId?: number;
  subscriptionId?: number;
  amountInr: number;
  status: 'PENDING' | 'SUCCESS' | 'FAILED';
  method: string;
  reference?: string;
  createdAt: string;
}
