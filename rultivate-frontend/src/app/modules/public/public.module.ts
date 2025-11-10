import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { PublicRoutingModule } from './public-routing.module';
import { SharedModule } from '../../shared/shared.module';
import { HomeComponent } from './pages/home/home.component';
import { AboutComponent } from './pages/about/about.component';
import { HowItWorksComponent } from './pages/how-it-works/how-it-works.component';
import { CategoriesComponent } from './pages/categories/categories.component';
import { PricingComponent } from './pages/pricing/pricing.component';
import { FaqComponent } from './pages/faq/faq.component';
import { ContactComponent } from './pages/contact/contact.component';
import { VendorDirectoryComponent } from './pages/vendor-directory/vendor-directory.component';
import { VendorDetailComponent } from './pages/vendor-detail/vendor-detail.component';

@NgModule({
  declarations: [
    HomeComponent,
    AboutComponent,
    HowItWorksComponent,
    CategoriesComponent,
    PricingComponent,
    FaqComponent,
    ContactComponent,
    VendorDirectoryComponent,
    VendorDetailComponent
  ],
  imports: [CommonModule, FormsModule, ReactiveFormsModule, PublicRoutingModule, SharedModule]
})
export class PublicModule {}
