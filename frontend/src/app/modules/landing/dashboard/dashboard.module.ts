import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { SharedModule } from 'app/shared/shared.module';
import { DashboardComponent } from './dashboard.component';
import { DashboardRoutes } from './dashboard.routing';
import { UpcomingCourseComponent } from './comoponents/upcoming-course/upcoming-course.component';
import { SwiperModule } from 'swiper/angular';
import { EnrolledCourseComponent } from './comoponents/enrolled-course/enrolled-course.component';
import { PastCertificateComponent } from './comoponents/past-certificate/past-certificate.component';
import { RecommendedCourseComponent } from './comoponents/recommended-course/recommended-course.component';
import { WelcomeBannerComponent } from './comoponents/welcome-banner/welcome-banner.component';



@NgModule({
    declarations: [DashboardComponent, UpcomingCourseComponent,  EnrolledCourseComponent, PastCertificateComponent, RecommendedCourseComponent, WelcomeBannerComponent],
    imports: [
        CommonModule,
        SwiperModule,
        RouterModule.forChild(DashboardRoutes),
        SharedModule,
    ],
})
export class DashboardModule {}
