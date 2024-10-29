import { DOCUMENT } from '@angular/common';
import { Component, Inject } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Course } from './models/course';
import { GetCourseDetailsService } from './services/get-course-details.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-course-list',
    templateUrl: './course-list.component.html',
    styleUrls: ['./course-list.component.scss'],
})
export class CourseListComponent {
    courseList: Course[]; //array of all courses
    currentPageCourses: Course[]; //array of courses for current page
    numberOfCoursesPerPage: number;
    indexOfFirstCourse: number;
    filteringCourse: boolean;
    loading: boolean = true;
    coursesSearching:boolean=false;
    lang: string = 'EN';

    searchedItem: string = '';

    constructor(
        private getCoursesApi: GetCourseDetailsService,
        private route: ActivatedRoute,
        private _router: Router,
        @Inject(DOCUMENT) private document: Document,
        private translateService:TranslateService
    ) {}

    ngOnInit() {
        this.document.body.scrollTop = 0;
        this.document.documentElement.scrollTop = 0;
        // if (localStorage.getItem('lang') == 'bn') this.lang = 'BN';
        // else this.lang = 'EN';

        this.currentPageCourses = [];
        this.filteringCourse = false;
        this.searchedItem = '';
        this.loading = true;
        this.courseList = [];
        this.numberOfCoursesPerPage = 10;
        this.indexOfFirstCourse = 0;

        this.route.queryParams.subscribe((params) => {
            if (params.search) this.searchedItem = params.search;
            if (this.searchedItem.length > 0) {
                this.filteringCourse = true;
            }
            if (!this.filteringCourse) {
                this.getCoursesApi
                    .getCourseList(this.lang)
                    .subscribe((successRes: any) => {
                        console.log("successRes",successRes);

                        this.loading = false;

                        let allCoursesData: any = successRes.courses
                            .filter(course => course.visible === 1);

                        this.fillCourseData(allCoursesData);
                    });
            } else {
                this.searchCourses();
            }
        });
    }

    searchCourses() {
        this.loading = false;
        this.coursesSearching = true;
        this.getCoursesApi
            .searchCourses(this.lang, this.searchedItem)
            .subscribe((response: any) => {
                console.log("response",response);
                this.coursesSearching = false;

                let allCoursesData: any = response.courses
                    .filter(course=>course.visible===1);
                
                this.fillCourseData(allCoursesData);

                this._router.navigate(['/courses'], {
                        queryParams: { search: this.searchedItem },
                    });
            });
    }

    goPrev() {
        this.document.body.scrollTop = 0;
        this.document.documentElement.scrollTop = 0;
        this.indexOfFirstCourse -= this.numberOfCoursesPerPage;
        this.setFirstCourseIndex();
    }

    goNext() {
        this.document.body.scrollTop = 0;
        this.document.documentElement.scrollTop = 0;
        this.indexOfFirstCourse += this.numberOfCoursesPerPage;
        this.setFirstCourseIndex();
    }

    setFirstCourseIndex() {
        this.currentPageCourses = this.courseList.slice(
            this.indexOfFirstCourse,
            this.indexOfFirstCourse + this.numberOfCoursesPerPage
        );
    }

    fillCourseData(courseList: any) {
        this.courseList = [];
        for (let course of courseList) {
            let currentCourse: Course = {} as Course;
            currentCourse.id = course.id;
            if (course.fullname) {
                //if displayname exists
                currentCourse.name = course.fullname;
            } else {
                currentCourse.name = '*'; //no course name available
            }

            if (this.translateService.getDefaultLang() === 'bn') {
                const bengaliName = course.customfields.find(field => field.shortname === 'cname').value;

                if (bengaliName?.length > 0) { currentCourse.name = bengaliName; }
                else {
                    currentCourse.name = '*'
                }
            }

            if (course.overviewfiles) {
                if (course.overviewfiles[0]) {
                    currentCourse.imageSrc = course.overviewfiles[0].fileurl;
                    currentCourse.imageSrc = currentCourse.imageSrc.replace(
                        'webservice/',
                        ''
                    );
                } else {
                    currentCourse.imageSrc =
                        'assets/images/home/CourseDetailsDesktop.webp'; //no image url found, so dummy image set
                }
            }

            currentCourse.instructors = [];
            if (course.contacts) {
                for (let user of course.contacts) {
                    currentCourse.instructors.push(user);
                }
            }

            this.courseList.push(currentCourse);
        }

        this.setFirstCourseIndex();
    }

    displayCourseDetails(id: any) {
        this._router.navigate(['course', id]);
    }
}
