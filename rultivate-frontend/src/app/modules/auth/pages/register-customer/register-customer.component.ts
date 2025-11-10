import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-register-customer',
  templateUrl: './register-customer.component.html'
})
export class RegisterCustomerComponent {
  error = '';

  form = this.fb.group({
    fullName: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    phone: ['', Validators.required],
    password: ['', [Validators.required, Validators.minLength(8)]],
    company: ['', Validators.required]
  });

  constructor(private fb: FormBuilder, private authService: AuthService, private router: Router) {}

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.authService.registerCustomer(this.form.value).subscribe({
      next: () => this.router.navigate(['/customer']),
      error: err => (this.error = err.error?.message || 'Unable to register. Please try again.')
    });
  }
}
