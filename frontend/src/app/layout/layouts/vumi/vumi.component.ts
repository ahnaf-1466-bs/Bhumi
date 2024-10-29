import { Component, OnDestroy, OnInit } from '@angular/core';
import { NavigationEnd, NavigationExtras, Router } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { AuthService } from 'app/core/auth/auth.service';
import { UserService } from 'app/core/user/user.service';
import { CourseDetailsService } from 'app/modules/landing/courses/course-details/course-details.service';
import { CompleteCourseService } from 'app/modules/landing/feedback-activity/services/complete-course.service';
import { BehaviorSubject, Observable, Subject, combineLatest } from 'rxjs';
import { VumiService } from './vumi.service';

@Component({
    selector: 'vumi-layout',
    templateUrl: './vumi.component.html',
    styleUrls: ['./vumi.component.scss'],
    // encapsulation: ViewEncapsulation.None,
})
export class VumiLayoutComponent implements OnDestroy, OnInit {
    private _unsubscribeAll: Subject<any> = new Subject<any>();

    constructor(
        private _translate: TranslateService,
        private _router: Router,
        private _authService: AuthService,
        private _vumi: VumiService,
        private _user: UserService,
        private _getCoursesService: CourseDetailsService,
        private completeCourseService: CompleteCourseService
    ) {
        this.lang = localStorage.getItem('lang') || 'en';
        this._translate.use(this.lang);
    }

    isExpand: boolean = false;
    menuIcon: string = 'heroicons_solid:menu';
    lang: string = '';

    searchCourses: string = '';
    signing: string = 'LOG_IN';
    isLogin: boolean = false;
    firstName: string;

    selectedLanguage: any;
    isFlagExpand: boolean = false;

    notificationCount = new BehaviorSubject<number>(0);
    notificationList = new BehaviorSubject<any[]>([]);

    languages: any[] = [
        {
            id: 1,
            img: 'assets/images/flags/bd.webp',
            name: 'bn',
        },
        {
            id: 2,
            img: 'assets/images/flags/en.webp',
            name: 'en',
        },
    ];

    courses: any[] = [];
    currentUrls: any[] = [];
    timerIds: any[] = [];

    id;
    result;

    showDiv: boolean = false;

    distance: any = 0;
    discountDistance: any;

    days;
    hours;
    minutes;
    seconds;
    sec;

    discountDays;
    discountHours;
    discountMinutes;
    discountSeconds;
    disSec;
    obs!: Observable<any>;

    userid;
    allNotifications: any[] = [];
    allNotificationsLength;

    unreadCount: number = 0;

    ngOnInit() {
        this.lang = localStorage.getItem('lang') || 'en';
        this._translate.use(this.lang);

        this._user.isLoggedIn().subscribe((res) => {
            if (res === true) {
                this.signing = 'LOG_OUT';
                this.isLogin = true;
            }
        });

        this.selectedLanguage =
            this.lang === 'bn' ? this.languages[0] : this.languages[1];

        this._vumi.getCourses().subscribe((res) => {
            // removes hidden courses
            this.courses = res.courses.filter(c=>c.visible===1);            

            // changes displayname of courses to their bengali name
            if (this._translate.getDefaultLang() === 'bn') {
                this.courses.forEach(c => {
                    const bengaliDisplayName = 
                        c.customfields.find(field => field.shortname === "cname").value;
                    c.displayname = bengaliDisplayName?.length>0 ? bengaliDisplayName : "*";
                });
            }
        });

        this.completeCourseService.getCourseCompletionEvent().subscribe(() => {
            this._vumi.getNotifications(this.userid).subscribe(
                (response) => {
                    // this.unreadCount = response.unread;
                    this.notificationCount.next(response.unreadcount);
                    if (response && response.notifications) {
                        const arr = [...response.notifications];
                        this.notificationList.next(arr);

                        this.allNotificationsLength =
                            this.allNotifications?.length;
                    } else {
                        // Handle the unexpected response format here, e.g. by setting a default value for this.allNotifications
                    }
                },
                (error) => {
                    // Handle the error here, e.g. by setting a default value for this.allNotifications
                }
            );
        });

        this.userid = localStorage.getItem('user-id') || '';

        this.notificationCount.subscribe((res) => (this.unreadCount = res));
        this.notificationList.subscribe((res) => (this.allNotifications = res));
        this._vumi.getNotifications(this.userid).subscribe(
            (response) => {
                // this.unreadCount = response.unread;
                this.notificationCount.next(response.unreadcount);
                if (response && response.notifications) {
                    const arr = [...response.notifications];
                    this.notificationList.next(arr);

                    this.allNotificationsLength = this.allNotifications?.length;
                } else {
                    // Handle the unexpected response format here, e.g. by setting a default value for this.allNotifications
                }
            },
            (error) => {
                // Handle the error here, e.g. by setting a default value for this.allNotifications
            }
        );

        this.currentUrls = this._router.url.split('/');

        if (this.currentUrls.includes('course')) {
            this.distance = 0;
            this.discountDistance = 0;
            this.id = this.currentUrls.at(-1);
            this.loadDiscount();
            this.timerIds = this.startTimers();
        } else {
            this.timerIds.forEach((timerId) => {
                clearInterval(timerId);
            });
        }

        this.id = this.currentUrls.pop();

        this._router.events.subscribe((event) => {
            this.showDiv = false;
            this.distance = 0;
            this.discountDistance = 0;
            if (event instanceof NavigationEnd) {
                let arr = event.url.split('/');
                this.currentUrls = arr;
                if (!arr.includes('course')) {
                    this.showDiv = false;
                    this.timerIds.forEach((timerId) => {
                        clearInterval(timerId);
                    });
                    return;
                } else {
                    this.timerIds = this.startTimers();
                }
                this.id = arr.pop();
                this.loadDiscount();
            }
        });
        this.firstName = localStorage.getItem('user-firstname');
    }

    loadDiscount() {
        let body = [
            { wsfunction: 'core_course_get_courses_by_field' },
            { field: 'id' },
            { value: this.id },
        ];
        const obs1 = this._getCoursesService.getDetails(body);

        let body2 = [
            {
                wsfunction: 'local_discount_get_expire_time_by_courseid',
            },
            { courseid: this.id },
        ];
        const obs2 = this._getCoursesService.getDetails(body2);
        const combine = combineLatest([obs1, obs2]);
        this.obs = combine;
        combine.subscribe(([res1, res2]) => {
            this.result = res1?.courses[0];
            this.distance = this.result?.startdate * 1000 - Date.now();
            this.sec = Math.floor(this.distance / 1000);
            this.days = Math.floor(this.sec / (3600 * 24));
            this.hours = Math.floor((this.sec % (3600 * 24)) / 3600);
            this.minutes = Math.floor((this.sec % 3600) / 60);
            this.seconds = this.sec % 60;
            this.discountDistance = res2?.timeexpired * 1000 - Date.now();
            this.disSec = Math.floor(this.discountDistance / 1000);
            this.discountDays = Math.floor(this.disSec / (3600 * 24));
            this.discountHours = Math.floor((this.disSec % (3600 * 24)) / 3600);
            this.discountMinutes = Math.floor((this.disSec % 3600) / 60);
            this.discountSeconds = this.disSec % 60;
            this.showDiv = true;
        });
    }

    startTimers() {
        const timer = setInterval(() => {
            this.sec -= 1;
        }, 1000);

        const timer2 = setInterval(() => {
            this.days = Math.floor(this.sec / (3600 * 24));
            this.hours = Math.floor((this.sec % (3600 * 24)) / 3600);
            this.minutes = Math.floor((this.sec % 3600) / 60);
            this.seconds = this.sec % 60;
        }, 1000);

        const timer3 = setInterval(() => {
            this.disSec -= 1;
        }, 1000);

        const timer4 = setInterval(() => {
            this.discountDays = Math.floor(this.disSec / (3600 * 24));
            this.discountHours = Math.floor((this.disSec % (3600 * 24)) / 3600);
            this.discountMinutes = Math.floor((this.disSec % 3600) / 60);
            this.discountSeconds = this.disSec % 60;
        }, 1000);

        return [timer, timer2, timer3, timer4];
    }

    markAllAsRead() {
        this._vumi.markAsRead(this.userid).subscribe((res) => {
            this.notificationCount.next(0);
            this._vumi.getNotifications(this.userid).subscribe(
                (response) => {
                    this.notificationCount.next(response.unreadcount);
                    if (response && response.notifications) {
                        const arr = [...response.notifications];
                        this.notificationList.next(arr);
                        this.allNotificationsLength =
                            this.allNotifications?.length;
                    } else {
                        // Handle the unexpected response format here, e.g. by setting a default value for this.allNotifications
                    }
                },
                (error) => {
                    // Handle the error here, e.g. by setting a default value for this.allNotifications
                }
            );
        });
    }

    closeDiv() {
        this.showDiv = false;
        this.timerIds.forEach((timerId) => {
            clearInterval(timerId);
        });
    }

    displayCourseDetails(id) {
        this.searchCourses = '';
        this._router.navigate(['course', id]);
    }

    goto(route: string) {
        if (route === 'login' && this.isLogin) {
            this._authService.signOut();
            this._router.navigate(['login']);
            return;
        }
        if (route === 'signup') {
            this._router.navigate(['signup']);
        }
        this._router.navigate([route]);
    }

    navigateToFooterPage(queryParamName: string) {
        const navigationExtras: NavigationExtras = {
            queryParams: { name: queryParamName },
            skipLocationChange: false, // set this to false to reload the current route
            replaceUrl: true, // set this to true to replace the current URL with the new one
        };
        if (queryParamName == 'faq') {
            this._router.navigate(['links', queryParamName]).then(() => {
                // once navigation is complete, scroll to top of the page
                window.scrollTo(0, 0);
            });
        } else {
            this._router.navigate(['links'], navigationExtras).then(() => {
                // once navigation is complete, scroll to top of the page
                window.scrollTo(0, 0);
            });
        }
    }

    expandMenu() {
        this.isExpand = !this.isExpand;
        if (this.menuIcon === 'heroicons_solid:menu') {
            this.menuIcon = 'heroicons_solid:x';
        } else {
            this.menuIcon = 'heroicons_solid:menu';
        }
    }

    expandIt() {
        this.isFlagExpand = !this.isFlagExpand;
    }

    selectIt(id: number, value: string) {
        this.isFlagExpand = false;
        this.selectedLanguage = this.languages[id];
        localStorage.setItem('lang', value);
        this._translate.use(value);
        window.location.reload();
    }

    // -----------------------------------------------------------------------------------------------------
    // @ Lifecycle hooks
    // -----------------------------------------------------------------------------------------------------

    /**
     * On destroy
     */
    ngOnDestroy(): void {
        // Unsubscribe from all subscriptions
        this._unsubscribeAll.next(null);
        this._unsubscribeAll.complete();
    }
}
