import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { VendorProfile } from '../../shared/models/vendor.model';
import { Rfq } from '../../shared/models/rfq.model';
import { Bid } from '../../shared/models/bid.model';
import { Order } from '../../shared/models/order.model';
import { VendorSubscriptionStatus, SubscriptionPlan } from '../../shared/models/subscription.model';

@Injectable({ providedIn: 'root' })
export class VendorService {
  constructor(private http: HttpClient) {}

  getPublicVendors(filters?: Record<string, string | number>): Observable<VendorProfile[]> {
    let params = new HttpParams();
    if (filters) {
      Object.keys(filters).forEach(key => {
        const value = filters[key];
        if (value !== undefined && value !== null && value !== '') {
          params = params.set(key, value as string);
        }
      });
    }
    return this.http.get<VendorProfile[]>(`${environment.apiUrl}/vendors/public`, { params });
  }

  getPublicVendor(slug: string): Observable<VendorProfile> {
    return this.http.get<VendorProfile>(`${environment.apiUrl}/vendors/${slug}`);
  }

  getProfile(): Observable<VendorProfile> {
    return this.http.get<VendorProfile>(`${environment.apiUrl}/vendor/profile`);
  }

  updateProfile(payload: Partial<VendorProfile>): Observable<VendorProfile> {
    return this.http.put<VendorProfile>(`${environment.apiUrl}/vendor/profile`, payload);
  }

  getVendorRfqs(): Observable<Rfq[]> {
    return this.http.get<Rfq[]>(`${environment.apiUrl}/vendor/rfqs`);
  }

  submitBid(payload: Partial<Bid>): Observable<Bid> {
    return this.http.post<Bid>(`${environment.apiUrl}/bids`, payload);
  }

  updateBid(bidId: number, payload: Partial<Bid>): Observable<Bid> {
    return this.http.put<Bid>(`${environment.apiUrl}/bids/${bidId}`, payload);
  }

  listBids(): Observable<Bid[]> {
    return this.http.get<Bid[]>(`${environment.apiUrl}/vendor/bids`);
  }

  listOrders(): Observable<Order[]> {
    return this.http.get<Order[]>(`${environment.apiUrl}/vendor/orders`);
  }

  getSubscriptionStatus(): Observable<VendorSubscriptionStatus> {
    return this.http.get<VendorSubscriptionStatus>(`${environment.apiUrl}/vendor/subscription`);
  }

  listPlans(): Observable<SubscriptionPlan[]> {
    return this.http.get<SubscriptionPlan[]>(`${environment.apiUrl}/subscription/plans`);
  }

  activatePlan(planId: number): Observable<VendorSubscriptionStatus> {
    return this.http.post<VendorSubscriptionStatus>(`${environment.apiUrl}/vendor/subscription/activate`, { planId });
  }
}
