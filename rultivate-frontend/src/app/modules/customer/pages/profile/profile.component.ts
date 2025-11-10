import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-customer-profile',
  templateUrl: './profile.component.html'
})
export class CustomerProfileComponent implements OnInit {
  message = '';

  form = this.fb.group({
    fullName: ['', Validators.required],
    company: ['', Validators.required],
    phone: ['', Validators.required],
    address: [''],
    city: [''],
    state: [''],
    pinCode: ['']
  });

  constructor(private fb: FormBuilder, private http: HttpClient) {}

  ngOnInit(): void {
    this.http.get<any>(`${environment.apiUrl}/customer/profile`).subscribe(profile => this.form.patchValue(profile));
  }

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.http.put(`${environment.apiUrl}/customer/profile`, this.form.value).subscribe(() => {
      this.message = 'Profile updated successfully';
    });
  }
}
