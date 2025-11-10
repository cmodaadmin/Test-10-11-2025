import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { AuthRoutingModule } from './auth-routing.module';
import { SharedModule } from '../../shared/shared.module';
import { LoginComponent } from './pages/login/login.component';
import { RegisterCustomerComponent } from './pages/register-customer/register-customer.component';
import { RegisterVendorComponent } from './pages/register-vendor/register-vendor.component';
import { ForgotPasswordComponent } from './pages/forgot-password/forgot-password.component';
import { ResetPasswordComponent } from './pages/reset-password/reset-password.component';

@NgModule({
  declarations: [
    LoginComponent,
    RegisterCustomerComponent,
    RegisterVendorComponent,
    ForgotPasswordComponent,
    ResetPasswordComponent
  ],
  imports: [CommonModule, ReactiveFormsModule, FormsModule, AuthRoutingModule, SharedModule]
})
export class AuthModule {}
