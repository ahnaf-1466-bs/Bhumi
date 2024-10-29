import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { VideoPdfActivityComponent } from './video-pdf-activity.component';
import { Route, RouterModule } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';
import { MatTooltipModule } from '@angular/material/tooltip';
import { SharedModule } from 'app/shared/shared.module';

const VideoPdfActivityRoute: Route[] = [
  {
      path     : '',
      component: VideoPdfActivityComponent
  }
];

@NgModule({
  declarations: [VideoPdfActivityComponent],
  imports: [
    CommonModule,
    RouterModule.forChild(VideoPdfActivityRoute),
    MatIconModule,
    MatTooltipModule,
    SharedModule
  ]
})
export class VideoPdfActivityModule { }
