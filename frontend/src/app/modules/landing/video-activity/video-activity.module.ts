import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MatIconModule } from '@angular/material/icon';
import { MatTooltipModule } from '@angular/material/tooltip';
import { RouterModule } from '@angular/router';
import { VideoActivityComponent } from './video-activity.component';
import { VideoActivityRoute } from './video-activity.routing';
import { SharedModule } from 'app/shared/shared.module';

@NgModule({
  declarations: [ VideoActivityComponent],
  imports: [
    CommonModule,
    MatTooltipModule,
    RouterModule.forChild(VideoActivityRoute),
    FormsModule,
    MatIconModule,
    SharedModule
  ]
})
export class VideoActivityModule { }
