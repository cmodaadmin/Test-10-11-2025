import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-register-vendor',
  templateUrl: './register-vendor.component.html'
})
export class RegisterVendorComponent {
  error = '';

  form = this.fb.group({
    companyName: ['', Validators.required],
    fullName: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    phone: ['', Validators.required],
    password: ['', [Validators.required, Validators.minLength(8)]],
    gstNumber: [''],
    panNumber: ['']
  });

  constructor(private fb: FormBuilder, private authService: AuthService, private router: Router) {}

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.authService.registerVendor(this.form.value).subscribe({
      next: () => this.router.navigate(['/vendor']),
      error: err => (this.error = err.error?.message || 'Unable to register. Please try again.')
    });
  }
}
