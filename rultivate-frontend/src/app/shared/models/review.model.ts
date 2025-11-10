export interface Review {
  id: number;
  orderId: number;
  vendorId: number;
  customerId: number;
  rating: number;
  comment?: string;
  isHidden: boolean;
  createdAt: string;
}
