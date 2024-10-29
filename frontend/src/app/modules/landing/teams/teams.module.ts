import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DirectorComponent } from './components/director/director.component';
import { FounderComponent } from './components/founder/founder.component';
import { InstructorComponent } from './components/instructor/instructor.component';
import { OperationComponent } from './components/operation/operation.component';
import { TeamsComponent } from './teams/teams.component';
import { RouterModule } from '@angular/router';
import { routes } from './teams.routing';
import { SwiperModule } from 'swiper/angular';
import { SharedModule } from 'app/shared/shared.module';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';





@NgModule({
  declarations: [
    DirectorComponent,
    FounderComponent, 
    InstructorComponent,
    OperationComponent,
    TeamsComponent,
  ],
  imports: [
    CommonModule,
    SharedModule,
    SwiperModule,
    MatButtonModule,
        MatIconModule,
       
        SwiperModule,
    RouterModule.forChild(routes),
  ]
})
export class TeamsModule { }
