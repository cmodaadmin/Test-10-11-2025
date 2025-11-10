import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { ConversationMessage } from '../../shared/models/message.model';

@Injectable({ providedIn: 'root' })
export class MessageService {
  constructor(private http: HttpClient) {}

  list(threadId: number): Observable<ConversationMessage[]> {
    return this.http.get<ConversationMessage[]>(`${environment.apiUrl}/messages/${threadId}`);
  }

  send(threadId: number, body: string): Observable<ConversationMessage> {
    return this.http.post<ConversationMessage>(`${environment.apiUrl}/messages/${threadId}`, { body });
  }
}
