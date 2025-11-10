import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { SubscriptionPlan } from '../../shared/models/subscription.model';

@Injectable({ providedIn: 'root' })
export class SubscriptionService {
  constructor(private http: HttpClient) {}

  listPlans(): Observable<SubscriptionPlan[]> {
    return this.http.get<SubscriptionPlan[]>(`${environment.apiUrl}/subscription/plans`);
  }

  createPlan(payload: Partial<SubscriptionPlan>): Observable<SubscriptionPlan> {
    return this.http.post<SubscriptionPlan>(`${environment.apiUrl}/subscription/plans`, payload);
  }

  updatePlan(id: number, payload: Partial<SubscriptionPlan>): Observable<SubscriptionPlan> {
    return this.http.put<SubscriptionPlan>(`${environment.apiUrl}/subscription/plans/${id}`, payload);
  }

  deletePlan(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${environment.apiUrl}/subscription/plans/${id}`);
  }
}
