import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-stat-card',
  templateUrl: './stat-card.component.html',
  styleUrls: ['./stat-card.component.scss']
})
export class StatCardComponent {
  @Input() label = '';
  @Input() value: string | number = '';
  @Input() trend?: string;
  @Input() icon = 'bi-graph-up';
}
