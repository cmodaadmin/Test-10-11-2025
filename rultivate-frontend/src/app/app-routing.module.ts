import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth.guard';
import { RoleGuard } from './core/guards/role.guard';

const routes: Routes = [
  {
    path: '',
    loadChildren: () => import('./modules/public/public.module').then(m => m.PublicModule)
  },
  {
    path: 'auth',
    loadChildren: () => import('./modules/auth/auth.module').then(m => m.AuthModule)
  },
  {
    path: 'customer',
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['customer'] },
    loadChildren: () => import('./modules/customer/customer.module').then(m => m.CustomerModule)
  },
  {
    path: 'vendor',
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['vendor'] },
    loadChildren: () => import('./modules/vendor/vendor.module').then(m => m.VendorModule)
  },
  {
    path: 'admin',
    canActivate: [AuthGuard, RoleGuard],
    data: { roles: ['super_admin', 'dept_vendor', 'dept_support', 'dept_operations', 'dept_finance', 'dept_content'] },
    loadChildren: () => import('./modules/admin/admin.module').then(m => m.AdminModule)
  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { scrollPositionRestoration: 'enabled' })],
  exports: [RouterModule]
})
export class AppRoutingModule {}
