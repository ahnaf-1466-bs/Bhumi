import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {MatGridListModule} from '@angular/material/grid-list';
import { CourseFeaturesComponent } from './course-features.component';
import { CourseSingleFeatureComponent } from '../course-single-feature/course-single-feature.component';
import { SharedModule } from 'app/shared/shared.module';

@NgModule({
  declarations: [
    CourseFeaturesComponent,
    CourseSingleFeatureComponent ],
  exports: [CourseFeaturesComponent],
  imports: [
    CommonModule,
    MatGridListModule,
    SharedModule
  ]
})
export class CourseFeaturesModule { }
