import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-contact',
  templateUrl: './contact.component.html'
})
export class ContactComponent {
  submitted = false;
  successMessage = '';

  form = this.fb.group({
    name: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    phone: ['', Validators.required],
    company: [''],
    message: ['', Validators.required]
  });

  constructor(private fb: FormBuilder, private http: HttpClient) {}

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.http.post<{ message: string }>(`${environment.apiUrl}/contact`, this.form.value).subscribe(response => {
      this.successMessage = response.message;
      this.submitted = true;
      this.form.reset();
    });
  }
}
