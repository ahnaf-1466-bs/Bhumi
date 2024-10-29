import {
    Component,
    EventEmitter,
    Input,
    Output,
    ViewChild,
} from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from 'app/core/auth/auth.service';
import Swiper from 'swiper';
import { SwiperComponent } from 'swiper/angular';
import { Course } from '../../models/course';
import { UpcomingCourseService } from '../../services/upcoming-course.service';

@Component({
    selector: 'app-upcoming-course',
    templateUrl: './upcoming-course.component.html',
    styleUrls: ['./upcoming-course.component.scss'],
})
export class UpcomingCourseComponent {
    @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
    @Input() upcomingCourseList: Course[] = [];
    @Output() courseStateChange = new EventEmitter<any>();

    loading: boolean = true;
    slides: { upcomingCourseList: Course[] }[] = [];

    constructor(
        private _router: Router,
        private _authService: AuthService,
        private upcomingCourseApi: UpcomingCourseService
    ) {}

    ngOnInit() {
        const swiperContainer = document.querySelector('.swiper-container');
        if (swiperContainer) {
            new Swiper('.swiper-container', {
                loop: false, // Set loop option to true
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
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

    ngAfterViewInit() {
        const swiperContainer = document.querySelector('.swiper-container');
        if (swiperContainer) {
            new Swiper('.swiper-container', {
                loop: false, // Set loop option to true
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
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
                } else if (res.exception || res.errorcode) {
                    this._authService.signOut();
                    this._router.navigate(['login']);
                    return;
                }
            });
    }

    displayCourseDetails(id) {
        this._router.navigate(['course', id]);
    }
}
