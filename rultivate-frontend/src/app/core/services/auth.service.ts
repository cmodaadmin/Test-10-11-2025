import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { environment } from '../../../environments/environment';
import { UserProfile } from '../../shared/models/user-profile.model';

interface AuthResponse {
  token: string;
  user: UserProfile;
}

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly storageKey = 'rultivate_token';
  private readonly userSubject = new BehaviorSubject<UserProfile | null>(null);
  readonly user$ = this.userSubject.asObservable();

  constructor(private http: HttpClient) {
    const token = localStorage.getItem(this.storageKey);
    if (token) {
      this.me().subscribe();
    }
  }

  login(payload: { email: string; password: string; role: string }): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${environment.apiUrl}/auth/login`, payload).pipe(
      tap(response => this.persistAuth(response))
    );
  }

  registerCustomer(payload: Record<string, unknown>): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${environment.apiUrl}/auth/register/customer`, payload).pipe(
      tap(response => this.persistAuth(response))
    );
  }

  registerVendor(payload: Record<string, unknown>): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${environment.apiUrl}/auth/register/vendor`, payload).pipe(
      tap(response => this.persistAuth(response))
    );
  }

  forgotPassword(email: string): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${environment.apiUrl}/auth/forgot-password`, { email });
  }

  resetPassword(token: string, password: string): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${environment.apiUrl}/auth/reset-password`, { token, password });
  }

  me(): Observable<UserProfile> {
    return this.http.get<UserProfile>(`${environment.apiUrl}/auth/me`).pipe(
      tap(user => this.userSubject.next(user))
    );
  }

  logout(): void {
    localStorage.removeItem(this.storageKey);
    this.userSubject.next(null);
  }

  get token(): string | null {
    return localStorage.getItem(this.storageKey);
  }

  private persistAuth(response: AuthResponse): void {
    localStorage.setItem(this.storageKey, response.token);
    this.userSubject.next(response.user);
  }
}
