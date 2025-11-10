import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Rfq } from '../../shared/models/rfq.model';
import { Bid } from '../../shared/models/bid.model';

@Injectable({ providedIn: 'root' })
export class RfqService {
  constructor(private http: HttpClient) {}

  create(payload: Partial<Rfq>): Observable<Rfq> {
    return this.http.post<Rfq>(`${environment.apiUrl}/rfqs`, payload);
  }

  update(id: number, payload: Partial<Rfq>): Observable<Rfq> {
    return this.http.put<Rfq>(`${environment.apiUrl}/rfqs/${id}`, payload);
  }

  list(): Observable<Rfq[]> {
    return this.http.get<Rfq[]>(`${environment.apiUrl}/rfqs`);
  }

  get(id: number): Observable<Rfq> {
    return this.http.get<Rfq>(`${environment.apiUrl}/rfqs/${id}`);
  }

  delete(id: number): Observable<{ message: string }> {
    return this.http.delete<{ message: string }>(`${environment.apiUrl}/rfqs/${id}`);
  }

  listBids(id: number): Observable<Bid[]> {
    return this.http.get<Bid[]>(`${environment.apiUrl}/rfqs/${id}/bids`);
  }

  inviteVendors(id: number, vendorIds: number[]): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${environment.apiUrl}/rfqs/${id}/invite`, { vendorIds });
  }
}
