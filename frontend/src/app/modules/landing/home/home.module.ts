import { NgModule } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { RouterModule } from '@angular/router';
import { LandingHomeComponent } from 'app/modules/landing/home/home.component';
import { landingHomeRoutes } from 'app/modules/landing/home/home.routing';
import { SharedModule } from 'app/shared/shared.module';
import { SwiperModule } from 'swiper/angular';

import { CommonModule } from '@angular/common';
import { MatDialogModule } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { ComingSoonModalComponent } from './components/components/digital-learning/coming-soon-modal/coming-soon-modal/coming-soon-modal.component';
import { DigitalLearningComponent } from './components/components/digital-learning/digital-learning.component';
import { BookASeatModal } from './components/components/experts-minds/book-a-seat-modal/book-a-seat-modal.component';
import { ExpertsMindsComponent } from './components/components/experts-minds/experts-minds.component';
import { KeyPartnersComponent } from './components/components/key-partners/key-partners.component';
import { NewsfeedComponent } from './components/components/newsfeed/newsfeed.component';
import { ShareModalComponent } from './components/components/newsfeed/share-modal/share-modal.component';
import { PopularCoursesComponent } from './components/components/popular-courses/popular-courses.component';
import { PotentialsComponent } from './components/components/potentials/potentials.component';
import { SlideContentComponent } from './components/components/slide-content/slide-content.component';
import { TestimonialsComponent } from './components/components/testimonials/testimonials.component';
import { UpcomingCourseComponent } from './components/components/upcoming-course/upcoming-course.component';
@NgModule({
    declarations: [
        LandingHomeComponent,
        PopularCoursesComponent,
        SlideContentComponent,
        ExpertsMindsComponent,
        BookASeatModal,
        PotentialsComponent,
        TestimonialsComponent,
        KeyPartnersComponent,
        DigitalLearningComponent,
        UpcomingCourseComponent,
        NewsfeedComponent,
        ComingSoonModalComponent,
        ShareModalComponent
    ],
    imports: [
        CommonModule,
        RouterModule.forChild(landingHomeRoutes),
        MatButtonModule,
        MatIconModule,
        SharedModule,
        SwiperModule,
        MatFormFieldModule,
        MatDialogModule,
        MatInputModule,
    ],
})
export class LandingHomeModule {}
