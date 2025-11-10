import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { VendorService } from '../../../core/services/vendor.service';
import { VendorProfile } from '../../../shared/models/vendor.model';

@Component({
  selector: 'app-vendor-detail',
  templateUrl: './vendor-detail.component.html'
})
export class VendorDetailComponent implements OnInit {
  vendor?: VendorProfile;

  constructor(private route: ActivatedRoute, private vendorService: VendorService) {}

  ngOnInit(): void {
    const slug = this.route.snapshot.paramMap.get('slug');
    if (slug) {
      this.vendorService.getPublicVendor(slug).subscribe(vendor => (this.vendor = vendor));
    }
  }
}
