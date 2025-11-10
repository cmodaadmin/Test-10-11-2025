import { Component, OnInit } from '@angular/core';
import { VendorService } from '../../../core/services/vendor.service';
import { VendorProfile } from '../../../shared/models/vendor.model';

@Component({
  selector: 'app-customer-browse-vendors',
  templateUrl: './browse-vendors.component.html'
})
export class CustomerBrowseVendorsComponent implements OnInit {
  vendors: VendorProfile[] = [];
  search = '';

  constructor(private vendorService: VendorService) {}

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.vendorService.getPublicVendors({ search: this.search }).subscribe(vendors => (this.vendors = vendors));
  }
}
