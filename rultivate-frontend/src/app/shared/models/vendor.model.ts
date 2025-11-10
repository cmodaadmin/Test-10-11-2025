export interface VendorProfile {
  id: number;
  userId: number;
  companyName: string;
  slug: string;
  gstNumber?: string;
  panNumber?: string;
  city?: string;
  state?: string;
  services: VendorService[];
  subscription?: VendorSubscription;
}

export interface VendorService {
  id: number;
  vendorId: number;
  name: string;
  description?: string;
  minPriceInr?: number;
}

export interface VendorSubscription {
  planId: number;
  planName: string;
  status: 'ACTIVE' | 'INACTIVE' | 'EXPIRED';
  validUntil?: string;
}
