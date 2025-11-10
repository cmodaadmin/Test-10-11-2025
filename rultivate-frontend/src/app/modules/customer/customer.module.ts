import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { CustomerRoutingModule } from './customer-routing.module';
import { SharedModule } from '../../shared/shared.module';
import { CustomerDashboardComponent } from './pages/dashboard/dashboard.component';
import { CustomerProfileComponent } from './pages/profile/profile.component';
import { CustomerBrowseVendorsComponent } from './pages/browse-vendors/browse-vendors.component';
import { CustomerRfqCreateComponent } from './pages/rfq-create/rfq-create.component';
import { CustomerRfqListComponent } from './pages/rfq-list/rfq-list.component';
import { CustomerRfqDetailComponent } from './pages/rfq-detail/rfq-detail.component';
import { CustomerBidComparisonComponent } from './pages/bid-comparison/bid-comparison.component';
import { CustomerOrdersComponent } from './pages/orders/orders.component';
import { CustomerMessagesComponent } from './pages/messages/messages.component';
import { CustomerNotificationsComponent } from './pages/notifications/notifications.component';
import { CustomerReviewsComponent } from './pages/reviews/reviews.component';
import { CustomerInvoicesComponent } from './pages/invoices/invoices.component';

@NgModule({
  declarations: [
    CustomerDashboardComponent,
    CustomerProfileComponent,
    CustomerBrowseVendorsComponent,
    CustomerRfqCreateComponent,
    CustomerRfqListComponent,
    CustomerRfqDetailComponent,
    CustomerBidComparisonComponent,
    CustomerOrdersComponent,
    CustomerMessagesComponent,
    CustomerNotificationsComponent,
    CustomerReviewsComponent,
    CustomerInvoicesComponent
  ],
  imports: [CommonModule, FormsModule, ReactiveFormsModule, CustomerRoutingModule, SharedModule]
})
export class CustomerModule {}
