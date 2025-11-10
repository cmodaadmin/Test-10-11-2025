import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.component.html'
})
export class ResetPasswordComponent {
  token = this.route.snapshot.paramMap.get('token') || '';
  message = '';

  form = this.fb.group({
    password: ['', [Validators.required, Validators.minLength(8)]]
  });

  constructor(private fb: FormBuilder, private route: ActivatedRoute, private router: Router, private authService: AuthService) {}

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.authService.resetPassword(this.token, this.form.value.password as string).subscribe(response => {
      this.message = response.message;
      setTimeout(() => this.router.navigate(['/auth/login']), 2500);
    });
  }
}
