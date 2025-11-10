import { Component, OnInit } from '@angular/core';
import { OrderService } from '../../../core/services/order.service';
import { RfqService } from '../../../core/services/rfq.service';
import { Order } from '../../../shared/models/order.model';
import { Rfq } from '../../../shared/models/rfq.model';

@Component({
  selector: 'app-customer-dashboard',
  templateUrl: './dashboard.component.html'
})
export class CustomerDashboardComponent implements OnInit {
  orders: Order[] = [];
  rfqs: Rfq[] = [];

  constructor(private orderService: OrderService, private rfqService: RfqService) {}

  ngOnInit(): void {
    this.orderService.listCustomerOrders().subscribe(orders => (this.orders = orders));
    this.rfqService.list().subscribe(rfqs => (this.rfqs = rfqs));
  }
}
