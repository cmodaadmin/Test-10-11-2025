import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { RfqService } from '../../../core/services/rfq.service';

@Component({
  selector: 'app-customer-rfq-create',
  templateUrl: './rfq-create.component.html'
})
export class CustomerRfqCreateComponent {
  message = '';

  form = this.fb.group({
    title: ['', Validators.required],
    description: ['', Validators.required],
    category: ['', Validators.required],
    budgetInr: [null, Validators.required],
    expectedDeliveryDate: ['', Validators.required]
  });

  constructor(private fb: FormBuilder, private rfqService: RfqService, private router: Router) {}

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.rfqService.create(this.form.value).subscribe(rfq => {
      this.message = 'RFQ created successfully';
      this.router.navigate(['/customer/rfqs', rfq.id]);
    });
  }
}
