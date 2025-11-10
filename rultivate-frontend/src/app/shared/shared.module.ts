import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { PageHeaderComponent } from './components/page-header/page-header.component';
import { StatCardComponent } from './components/stat-card/stat-card.component';

@NgModule({
  declarations: [PageHeaderComponent, StatCardComponent],
  imports: [CommonModule, RouterModule, FormsModule, ReactiveFormsModule],
  exports: [CommonModule, RouterModule, FormsModule, ReactiveFormsModule, PageHeaderComponent, StatCardComponent]
})
export class SharedModule {}
