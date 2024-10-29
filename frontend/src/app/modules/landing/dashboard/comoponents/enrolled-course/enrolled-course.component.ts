import { Component, Input, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import Swiper from 'swiper';
import { SwiperComponent } from 'swiper/angular';
import { Course } from '../../models/course';

@Component({
    selector: 'app-enrolled-course',
    templateUrl: './enrolled-course.component.html',
    styleUrls: ['./enrolled-course.component.scss'],
})
export class EnrolledCourseComponent {
    @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
    @Input() enrolledCourseList: Course[] = [];

    loading: boolean = true;
    slides: { enrolledCourseList: Course[] }[] = [];

    constructor(private _router: Router) {}

    ngOnInit() {}

    ngAfterViewInit() {
        const swiperContainer = document.querySelector('.swiper-container-enc');
        if (swiperContainer) {
            new Swiper('.swiper-container-enc', {
                loop: false, // Set loop option to false
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

    displayCourseDetails(id) {
        this._router.navigate(['course', id]);
    }
}
