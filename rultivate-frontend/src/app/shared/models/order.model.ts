export interface Order {
  id: number;
  rfqId: number;
  customerId: number;
  vendorId: number;
  bidId: number;
  status: 'PENDING' | 'ACCEPTED' | 'IN_PROGRESS' | 'COMPLETED' | 'CANCELLED';
  totalAmountInr: number;
  createdAt: string;
  updatedAt: string;
}

export interface OrderItem {
  id: number;
  orderId: number;
  description: string;
  amountInr: number;
  quantity: number;
}
