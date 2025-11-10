import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { NotificationItem } from '../../shared/models/message.model';

@Injectable({ providedIn: 'root' })
export class NotificationService {
  constructor(private http: HttpClient) {}

  list(): Observable<NotificationItem[]> {
    return this.http.get<NotificationItem[]>(`${environment.apiUrl}/notifications`);
  }

  markAsRead(id: number): Observable<NotificationItem> {
    return this.http.post<NotificationItem>(`${environment.apiUrl}/notifications/${id}/read`, {});
  }
}
