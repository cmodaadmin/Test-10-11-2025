import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Bid } from '../../shared/models/bid.model';
import { Order } from '../../shared/models/order.model';

@Injectable({ providedIn: 'root' })
export class BidService {
  constructor(private http: HttpClient) {}

  acceptBid(bidId: number): Observable<Order> {
    return this.http.post<Order>(`${environment.apiUrl}/bids/${bidId}/accept`, {});
  }

  listVendorBids(): Observable<Bid[]> {
    return this.http.get<Bid[]>(`${environment.apiUrl}/vendor/bids`);
  }

  listCustomerBids(rfqId: number): Observable<Bid[]> {
    return this.http.get<Bid[]>(`${environment.apiUrl}/rfqs/${rfqId}/bids`);
  }
}
