import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Order } from '../../shared/models/order.model';
import { PaymentRecord } from '../../shared/models/payment.model';

@Injectable({ providedIn: 'root' })
export class OrderService {
  constructor(private http: HttpClient) {}

  listCustomerOrders(): Observable<Order[]> {
    return this.http.get<Order[]>(`${environment.apiUrl}/orders`);
  }

  getOrder(id: number): Observable<Order> {
    return this.http.get<Order>(`${environment.apiUrl}/orders/${id}`);
  }

  listPayments(orderId: number): Observable<PaymentRecord[]> {
    return this.http.get<PaymentRecord[]>(`${environment.apiUrl}/orders/${orderId}/payments`);
  }
}
