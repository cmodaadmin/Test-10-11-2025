import { Component, OnInit } from '@angular/core';
import { SubscriptionService } from '../../../core/services/subscription.service';
import { SubscriptionPlan } from '../../../shared/models/subscription.model';

@Component({
  selector: 'app-pricing',
  templateUrl: './pricing.component.html'
})
export class PricingComponent implements OnInit {
  plans: SubscriptionPlan[] = [];

  constructor(private subscriptionService: SubscriptionService) {}

  ngOnInit(): void {
    this.subscriptionService.listPlans().subscribe(plans => (this.plans = plans));
  }
}
