import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { CourseService } from '../../../services/course.service';
import { GetCourseDetailsService } from 'app/modules/landing/course-list/services/get-course-details.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-popular-courses',
    templateUrl: './popular-courses.component.html',
    styleUrls: ['./popular-courses.component.scss'],
})
export class PopularCoursesComponent {
    constructor(private _router: Router,
        private _getCourses: CourseService,
        private _getCourseDetails:GetCourseDetailsService,
        private translateService: TranslateService
        ) {}

    popular_courses: any[];

    ngOnInit() {
        let body = [{ wsfunction: 'bs_webservicesuite_get_popular_courses' }];

        this._getCourses.getDetails(body).subscribe((response) => {
            console.log("response",response);
            
            this._getCourseDetails.getAllCoursesList().subscribe((courses: any) => {
                const visibleCoursesIDs = courses.filter(course => course.visible === 1)
                    .map(course => course.id);

                this.popular_courses = response?.popular_courses
                    ?.filter(popularCourse => visibleCoursesIDs.includes(popularCourse.id));

                    if(this.translateService.getDefaultLang()==='bn')
                    {
                        this.popular_courses.forEach(course=>{
                            const bengaliName = course.customfields.find(field=>field.shortname==='cname').value;
                        const bengaliDescription = course.customfields.find(field=>field.shortname==='cdescription').value;

                            course.fullname =  bengaliName?.length>0 ? bengaliName: '*';
                            course.summary = bengaliDescription?.length>0 ? bengaliDescription: '*';
                        })
                    }
            })
        });
    }

    displayCourseDetails(id) {
        this._router.navigate(['course', id]);
    }
}
