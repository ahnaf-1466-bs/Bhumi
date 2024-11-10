import { Component, EventEmitter, Output, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { Course } from 'app/modules/landing/dashboard/models/course';
import { UpcomingCourseService } from 'app/modules/landing/dashboard/services/upcoming-course.service';
import { SwiperComponent } from 'swiper/angular';

import Swiper from 'swiper';
import { GetCourseDetailsService } from 'app/modules/landing/course-list/services/get-course-details.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-upcoming-course',
    templateUrl: './upcoming-course.component.html',
    styleUrls: ['./upcoming-course.component.scss'],
})
export class UpcomingCourseComponent {
    @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
    upcomingCourseList: Course[] = [];
    slides: { upcomingCourseList: Course[] }[] = [];
    @Output() courseStateChange = new EventEmitter<any>();
    loading: boolean = true;

    constructor(
        private upcomingCourseApi: UpcomingCourseService,
        private _router: Router,
        private getCourseDetails:GetCourseDetailsService,
        private translateService: TranslateService
    ) {}

    ngOnInit() {
        this.upcomingCourseApi.getUpcomingCourses().subscribe((res: any) => {
            this.getCourseDetails.getAllCoursesList().subscribe((courses:any)=>{
                const visibleCourseIDs = courses.filter(course => course.visible === 1)
                    .map(course=>course.id);
                
                    if (res.future_courses) {
                        for (let upcomingCourse of res.future_courses
                                ?.filter(course=>visibleCourseIDs.includes(course.id))) {
                            let course = {} as Course;
                            course.id = upcomingCourse.id;
                            if(this.translateService.getDefaultLang()==='bn')
                            {
                                const bengaliName = upcomingCourse.customfields.find(field=>field.shortname==='cname').value;
                                const bengaliDescription = upcomingCourse.customfields.find(field=>field.shortname==='cdescription').value;

                                course.fullname = bengaliName?.length>0 ? bengaliName: '*';
                                course.summary = bengaliDescription?.length>0 ? bengaliDescription: '*';
                            }
                            else{
                                course.fullname = upcomingCourse.fullname;
                                course.summary = upcomingCourse.summary;
                            }
                            course.picurl = upcomingCourse.overviewfiles[0].fileurl;
                            course.picurl = course.picurl.replaceAll('/webservice', '');
                            this.upcomingCourseList.push(course);
                        }
                    }
            })
            
            
        });
    }

    ngAfterViewInit() {
        const swiperContainer = document.querySelector('.swiper-container-upc');
        if (swiperContainer) {
            new Swiper('.swiper-container-upc', {
                loop: false, // Set loop option to true
                pagination: {
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    0: {
                        slidesPerView: 1,
                        spaceBetween: 5,
                    },
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 5,
                    },
                    925: {
                        slidesPerView: 2,
                        spaceBetween: 8,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 10,
                    },
                },
            });
        }
    }

    displayCourseDetails(id) {
        this._router.navigate(['course', id]);
    }

    setFavourite(courseID, favouriteValue) {
        this.upcomingCourseApi
            .setFavourite(courseID, favouriteValue)
            .subscribe((res: any) => {
                if (!res.exception) {
                    for (let course of this.upcomingCourseList) {
                        if (course.id == courseID) {
                            if (favouriteValue == 1) course.favourite = true;
                            else course.favourite = false;

                            this.courseStateChange.emit(course);
                        }
                    }
                }
            });
    }
}
