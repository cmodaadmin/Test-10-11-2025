import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
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

const routes: Routes = [
  { path: '', component: CustomerDashboardComponent },
  { path: 'profile', component: CustomerProfileComponent },
  { path: 'vendors', component: CustomerBrowseVendorsComponent },
  { path: 'rfqs/new', component: CustomerRfqCreateComponent },
  { path: 'rfqs', component: CustomerRfqListComponent },
  { path: 'rfqs/:id', component: CustomerRfqDetailComponent },
  { path: 'rfqs/:id/bids', component: CustomerBidComparisonComponent },
  { path: 'orders', component: CustomerOrdersComponent },
  { path: 'messages', component: CustomerMessagesComponent },
  { path: 'notifications', component: CustomerNotificationsComponent },
  { path: 'reviews', component: CustomerReviewsComponent },
  { path: 'invoices', component: CustomerInvoicesComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CustomerRoutingModule {}
