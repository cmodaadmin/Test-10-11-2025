import { Component } from '@angular/core';
import { AuthService } from './core/services/auth.service';
import { Observable } from 'rxjs';
import { UserProfile } from './shared/models/user-profile.model';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  title = 'Rultivate';
  user$: Observable<UserProfile | null>;

  constructor(private authService: AuthService) {
    this.user$ = this.authService.user$;
  }

  logout(): void {
    this.authService.logout();
  }
}
