import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, Router, UrlTree } from '@angular/router';
import { Observable, map } from 'rxjs';
import { AuthService } from '../services/auth.service';

@Injectable({ providedIn: 'root' })
export class RoleGuard implements CanActivate {
  constructor(private authService: AuthService, private router: Router) {}

  canActivate(route: ActivatedRouteSnapshot): Observable<boolean | UrlTree> {
    const allowedRoles = route.data['roles'] as string[] | undefined;
    if (!allowedRoles) {
      return new Observable(observer => {
        observer.next(true);
        observer.complete();
      });
    }

    return this.authService.user$.pipe(
      map(user => {
        if (user && user.roles.some(role => allowedRoles.includes(role))) {
          return true;
        }
        return this.router.createUrlTree(['/auth/login']);
      })
    );
  }
}
