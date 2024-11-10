import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MatExpansionModule } from '@angular/material/expansion';
import { MatIconModule } from '@angular/material/icon';
import { MatSidenavModule } from '@angular/material/sidenav';
import { RouterModule } from '@angular/router';
import { CourseActivityComponent } from './course-activity.component';
import { CourseActivityRoutes } from './course-activity.routing';
import { CustomAccordionModule } from './custom-accordion/custom-accordion.module';
import {MatDialogModule} from '@angular/material/dialog';
import { IncompleteModal } from './incomplete-modal/incomplete-modal.component';
import { SharedModule } from 'app/shared/shared.module';

@NgModule({
  declarations: [
    CourseActivityComponent,
    IncompleteModal
  ],
  imports: [
    CommonModule,
    RouterModule.forChild(CourseActivityRoutes),
    MatExpansionModule,
    MatIconModule,
    MatSidenavModule,
    MatButtonModule,
    CustomAccordionModule,
    MatDialogModule,
    SharedModule
  ]
})
export class CourseActivityModule { }
