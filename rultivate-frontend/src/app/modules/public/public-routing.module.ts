import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from './pages/home/home.component';
import { AboutComponent } from './pages/about/about.component';
import { HowItWorksComponent } from './pages/how-it-works/how-it-works.component';
import { CategoriesComponent } from './pages/categories/categories.component';
import { PricingComponent } from './pages/pricing/pricing.component';
import { FaqComponent } from './pages/faq/faq.component';
import { ContactComponent } from './pages/contact/contact.component';
import { VendorDirectoryComponent } from './pages/vendor-directory/vendor-directory.component';
import { VendorDetailComponent } from './pages/vendor-detail/vendor-detail.component';

const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'about', component: AboutComponent },
  { path: 'how-it-works', component: HowItWorksComponent },
  { path: 'categories', component: CategoriesComponent },
  { path: 'pricing', component: PricingComponent },
  { path: 'faq', component: FaqComponent },
  { path: 'contact', component: ContactComponent },
  { path: 'vendors', component: VendorDirectoryComponent },
  { path: 'vendors/:slug', component: VendorDetailComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PublicRoutingModule {}
