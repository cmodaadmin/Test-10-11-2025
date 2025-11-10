import { Component, OnInit } from '@angular/core';
import { CmsService, CmsPage } from '../../../core/services/cms.service';

@Component({
  selector: 'app-faq',
  templateUrl: './faq.component.html'
})
export class FaqComponent implements OnInit {
  faqs: CmsPage[] = [];

  constructor(private cmsService: CmsService) {}

  ngOnInit(): void {
    this.cmsService.listPages().subscribe(pages => {
      this.faqs = pages.filter(page => page.slug.startsWith('faq'));
    });
  }
}
