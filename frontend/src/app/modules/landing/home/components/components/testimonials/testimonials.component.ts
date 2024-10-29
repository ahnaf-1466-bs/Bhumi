import { Component, Input, ViewChild, ViewEncapsulation } from '@angular/core';
import SwiperCore, { Virtual } from 'swiper';
import { SwiperComponent } from 'swiper/angular';
import { Feedback } from '../../../models/feedback';

SwiperCore.use([Virtual]);
@Component({
    selector: 'app-testimonials',
    templateUrl: './testimonials.component.html',
    styleUrls: ['./testimonials.component.scss'],
    encapsulation: ViewEncapsulation.None,
})
export class TestimonialsComponent {
    @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
    @ViewChild('swiper2', { static: false }) swiper2?: SwiperComponent;
    @Input() feedbacks: Feedback[];

    constructor() {}

    curIndex: number = 0;
    isBengali: boolean = false;

    ngOnInit() {
        this.curIndex = 0;
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }
    }

    slidePrevTestimonials() {
        if (this.curIndex > 0) {
            this.curIndex--;
            this.swiper.swiperRef.slidePrev(100);
        } else this.curIndex = 0;
    }

    slideNextExpertsPhone() {
        if (this.curIndex == this.feedbacks.length - 1)
            this.curIndex = this.feedbacks.length - 1;
        else this.curIndex++;
        this.swiper2.swiperRef.slideNext(100);
    }

    slideNextTestimonials() {
        if (this.curIndex == this.feedbacks.length - 1)
            this.curIndex = this.feedbacks.length - 1;
        else this.curIndex++;
        this.swiper.swiperRef.slideNext(100);
    }

    slidePrevExpertsPhone() {
        if (this.curIndex > 0) {
            this.curIndex--;
            this.swiper.swiperRef.slidePrev(100);
        } else this.curIndex = 0;
    }
}
