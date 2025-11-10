import { Component, OnInit } from '@angular/core';
import { VendorService } from '../../../core/services/vendor.service';
import { VendorProfile } from '../../../shared/models/vendor.model';

@Component({
  selector: 'app-vendor-directory',
  templateUrl: './vendor-directory.component.html'
})
export class VendorDirectoryComponent implements OnInit {
  vendors: VendorProfile[] = [];
  loading = false;
  searchTerm = '';

  constructor(private vendorService: VendorService) {}

  ngOnInit(): void {
    this.fetchVendors();
  }

  fetchVendors(): void {
    this.loading = true;
    this.vendorService.getPublicVendors({ search: this.searchTerm }).subscribe(vendors => {
      this.vendors = vendors;
      this.loading = false;
    });
  }
}
