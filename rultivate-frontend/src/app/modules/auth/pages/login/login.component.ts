import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html'
})
export class LoginComponent {
  error = '';
  role = 'customer';

  form = this.fb.group({
    email: ['', [Validators.required, Validators.email]],
    password: ['', Validators.required]
  });

  constructor(private fb: FormBuilder, private authService: AuthService, private router: Router) {}

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.authService.login({ ...this.form.value, role: this.role } as any).subscribe({
      next: () => this.router.navigate(['/']),
      error: err => (this.error = err.error?.message || 'Unable to login. Please try again.')
    });
  }
}
