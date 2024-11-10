import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { SharedModule } from 'app/shared/shared.module';
import { SwiperModule } from 'swiper/angular';
import { CertificateComponent } from './components/certificate/certificate.component';
import { CourseFeaturesModule } from './components/course-features/course-features.module';
import { CourseOverviewComponent } from './components/course-overview/course-overview.component';
import { LearnFromSlideComponent } from './components/learn-from-slide/learn-from-slide.component';
import { LearnFromComponent } from './components/learn-from/learn-from.component';
import { LiveVideoComponent } from './components/live-video/live-video.component';
import { ProgramNextBatchComponent } from './components/program-next-batch/program-next-batch.component';
import { ProgramSingleComponent } from './components/program-single/program-single.component';
import { ProgramSlideComponent } from './components/program-slide/program-slide.component';
import { ProgramStructureComponent } from './components/program-structure/program-structure.component';
import { SlideContentComponent } from './components/slide-content/slide-content.component';
import { SyllabusSingleComponent } from './components/syllabus-single/syllabus-single.component';
import { SyllabusComponent } from './components/syllabus/syllabus.component';
import { WhatWillYouLearnComponent } from './components/what-will-you-learn/what-will-you-learn.component';
import { WhoWillBeBenefitedComponent } from './components/who-will-be-benefited/who-will-be-benefited.component';
import { CoursesComponent } from './courses.component';
import { CoursesRoutes } from './courses.routing';

@NgModule({
    declarations: [
        CoursesComponent,
        CourseOverviewComponent,
        WhatWillYouLearnComponent,
        ProgramStructureComponent,
        ProgramSingleComponent,
        ProgramNextBatchComponent,
        LiveVideoComponent,
        WhoWillBeBenefitedComponent,
        SyllabusComponent,
        SyllabusSingleComponent,
        CertificateComponent,
        LearnFromComponent,
        LearnFromSlideComponent,
        SlideContentComponent,
        ProgramSlideComponent,
    ],
    imports: [
        CommonModule,
        CourseFeaturesModule,
        RouterModule.forChild(CoursesRoutes),
        SwiperModule,
        SharedModule,
    ],
})
export class CoursesModule {}
