import { ComponentFixture, TestBed } from '@angular/core/testing';
import { CertificateComponent } from './components/certificate/certificate.component';
import { CourseFeaturesComponent } from './components/course-features/course-features.component';
import { CourseFeaturesModule } from './components/course-features/course-features.module';
import { CourseOverviewComponent } from './components/course-overview/course-overview.component';
import { LearnFromSingleComponent } from './components/learn-from-single/learn-from-single.component';
import { LearnFromComponent } from './components/learn-from/learn-from.component';
import { LiveVideoComponent } from './components/live-video/live-video.component';
import { ProgramSingleComponent } from './components/program-single/program-single.component';
import { ProgramStructureComponent } from './components/program-structure/program-structure.component';
import { SlideModule } from './components/slide/slide.module';
import { SyllabusSingleComponent } from './components/syllabus-single/syllabus-single.component';
import { SyllabusComponent } from './components/syllabus/syllabus.component';
import { WhatWillYouLearnComponent } from './components/what-will-you-learn/what-will-you-learn.component';
import { WhoWillBeBenefitedComponent } from './components/who-will-be-benefited/who-will-be-benefited.component';

import { CoursesComponent } from './courses.component';

describe('CoursesComponent', () => {
    let component: CoursesComponent;
    let fixture: ComponentFixture<CoursesComponent>;

    beforeEach(async () => {
        await TestBed.configureTestingModule({
            declarations: [
                CoursesComponent,
                CourseOverviewComponent,
                WhatWillYouLearnComponent,
                ProgramStructureComponent,
                ProgramSingleComponent,
                LiveVideoComponent,
                WhoWillBeBenefitedComponent,
                SyllabusComponent,
                SyllabusSingleComponent,
                CertificateComponent,
                LearnFromComponent,
                LearnFromSingleComponent,
            ],
            imports: [
                CourseFeaturesModule,
                SlideModule
            ],
        }).compileComponents();

        fixture = TestBed.createComponent(CoursesComponent);
        component = fixture.componentInstance;
        fixture.detectChanges();
    });

    it('should create', () => {
        expect(component).toBeTruthy();
    });
});
