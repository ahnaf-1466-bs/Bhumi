import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { SharedModule } from 'app/shared/shared.module';
import { CourseListItemComponent } from './components/course-list-item/course-list-item.component';
import { CourseListComponent } from './course-list.component';
import { CourseListRoutes } from './course-list.routing';
import { MatIconModule } from '@angular/material/icon';


@NgModule({
    declarations: [CourseListComponent, CourseListItemComponent],
    imports: [
        CommonModule,
        SharedModule,
        MatIconModule,
        RouterModule.forChild(CourseListRoutes),
    ],
})
export class CourseListModule {}
