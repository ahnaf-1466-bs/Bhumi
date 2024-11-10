import { Component, Input, ViewChild } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import SwiperCore, { Virtual } from 'swiper';
import { SwiperComponent } from 'swiper/angular';
import { Partner } from '../../../models/partner';

// install Swiper modules
SwiperCore.use([Virtual]);

@Component({
    selector: 'app-key-partners',
    templateUrl: './key-partners.component.html',
    styleUrls: ['./key-partners.component.scss'],
    // encapsulation: ViewEncapsulation.None
})
export class KeyPartnersComponent {
    @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
    @Input() partners: Partner[];

    constructor(private sanitize: DomSanitizer) {}

    isBengali: boolean = false;

    ngOnInit() {
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }
    }

    slideNext() {
        this.swiper.swiperRef.slideNext(100);
    }

    slidePrev() {
        this.swiper.swiperRef.slidePrev(100);
    }
}
