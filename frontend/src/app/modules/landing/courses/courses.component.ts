import { DOCUMENT } from '@angular/common';
import { Component, Inject, ViewEncapsulation } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
import { EnrolledCourseService } from './services/enrolled-course.service';

import { TranslateService } from '@ngx-translate/core';
import SwiperCore, {
    A11y,
    Autoplay,
    Navigation,
    Pagination,
    Scrollbar,
} from 'swiper';
import { CourseDetailsService } from './course-details/course-details.service';

SwiperCore.use([Navigation, Pagination, Scrollbar, A11y, Autoplay]);

@Component({
    selector: 'app-courses',
    templateUrl: './courses.component.html',
    styleUrls: ['./courses.component.scss'],
    encapsulation: ViewEncapsulation.None,
})
export class CoursesComponent {
    mobile: boolean;
    loading: boolean;
    enrolled: boolean = false;

    constructor(
        private sanitize: DomSanitizer,
        private _getCoursesService: CourseDetailsService,
        private route: ActivatedRoute,
        private enrolledCourseApi: EnrolledCourseService,
        @Inject(DOCUMENT) private document: Document,
        private router: Router,
        private translateService:TranslateService
    ) {}

    result;
    id;
    image;
    courseResponse;
    courseResponse2;
    background_img_home_desktopView;

    ngOnInit() {
        this.document.body.scrollTop = 0;
        this.document.documentElement.scrollTop = 0;

        this.loading = true;

        if (window.screen.width <= 820) {
            this.mobile = true;
        } else {
            this.mobile = false;
        }

        this.route.paramMap.subscribe((params: ParamMap) => {
            this.id = +params.get('id');
            let body = [
                { wsfunction: 'core_course_get_courses_by_field' },
                { field: 'id' },
                { value: this.id },
            ];

            this._getCoursesService.getDetails(body).subscribe((response) => {
                // if course is hidden, then redirect to landing page
                if(response?.courses[0]?.visible !== 1)
                {
                    this.router.navigate(['']);
                }
                
                this.result = response.courses[0];

                // if language is bengali change name and description to bengali 
                if (this.translateService.getDefaultLang() === 'bn') {
                    const bengaliDescription = this.result.customfields.find(field => field.shortname === 'cdescription').value;
                    const bengaliName = this.result.customfields.find(field => field.shortname === 'cname').value;

                    this.result.fullname = bengaliName?.length>0 ? bengaliName : '*';
                    this.result.summary = bengaliDescription?.length>0 ? bengaliDescription : '*';
                }
                
                this.courseResponse2 = this.result;

                let img = this.result.overviewfiles[0];

                this.image = img
                    ? img?.fileurl.replace('/webservice', '')
                    : 'assets/images/home/CourseDetailsDesktop.webp';
                this.background_img_home_desktopView = this.image;
               
                let body2 = [
                    { wsfunction: 'mod_syllabusoverview_get_details_by_courseid' },
                    { courseid: this.id },
                ];
                this._getCoursesService.getDetails(body2).subscribe((response2) => {           
                    if ((response2 && (response2?.course_image!==undefined) && (response2?.course_image?.length > 0 ))) {
                        let unsanitizedImg = response2.course_image[0].courseimg;
                        this.background_img_home_desktopView = unsanitizedImg;
                    }
                    this.courseResponse = response2;
                    
                    this.loading = false;
                });
            });

            let body2 = [
                { wsfunction: 'mod_syllabusoverview_get_details_by_courseid' },
                { courseid: this.id },
            ];

            this.enrolledCourseApi.getEnrolledCourses().subscribe(
                (res: any) => {
                    if (Array.isArray(res)) {
                        // check if res is an array
                        for (let course of res) {
                            if (course.id == this.id) {
                                this.enrolled = true;
                            }
                        }
                    }
                },
                (error) => {
                    console.log('Error fetching enrolled courses:', error);
                }
            );
        });
    }
}
