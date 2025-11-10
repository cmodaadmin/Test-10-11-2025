export interface SubscriptionPlan {
  id: number;
  name: string;
  description?: string;
  priceInr: number;
  billingCycle: 'MONTHLY' | 'QUARTERLY' | 'YEARLY';
  maxRfqsPerMonth?: number;
  maxTeamMembers?: number;
  isActive: boolean;
}

export interface VendorSubscriptionStatus {
  plan?: SubscriptionPlan;
  status: 'ACTIVE' | 'INACTIVE' | 'EXPIRED';
  activatedAt?: string;
  expiresAt?: string;
}
