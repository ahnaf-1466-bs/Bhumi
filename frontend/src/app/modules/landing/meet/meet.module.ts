import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MeetRoutingModule } from './meet-routing.module';
import { FormsModule } from '@angular/forms';

import { MeetingCredentialsComponent } from './meeting-credentials/meeting-credentials.component';
import { MatIconModule } from '@angular/material/icon';
import { SharedModule } from 'app/shared/shared.module';


@NgModule({
  declarations: [
    MeetingCredentialsComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    MeetRoutingModule,
    MatIconModule,
    SharedModule
  ]
})
export class MeetModule { }
