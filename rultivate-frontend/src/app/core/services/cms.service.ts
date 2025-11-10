import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface CmsPage {
  id: number;
  slug: string;
  title: string;
  content: string;
  status: 'DRAFT' | 'PUBLISHED';
  updatedAt: string;
}

@Injectable({ providedIn: 'root' })
export class CmsService {
  constructor(private http: HttpClient) {}

  listPages(): Observable<CmsPage[]> {
    return this.http.get<CmsPage[]>(`${environment.apiUrl}/cms/pages`);
  }

  getPage(slug: string): Observable<CmsPage> {
    return this.http.get<CmsPage>(`${environment.apiUrl}/cms/pages/${slug}`);
  }

  updatePage(slug: string, payload: Partial<CmsPage>): Observable<CmsPage> {
    return this.http.put<CmsPage>(`${environment.apiUrl}/cms/pages/${slug}`, payload);
  }
}
