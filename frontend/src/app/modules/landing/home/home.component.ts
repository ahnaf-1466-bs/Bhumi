import { Component, ViewEncapsulation } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { Feedback } from './models/feedback';
import { Newsfeed } from './models/newsfeed';
import { Partner } from './models/partner';
import { GetContentsApiService } from './services/get-contents-api.service';
import { NewsfeedApiService } from './services/newsfeed-api.service';

import SwiperCore, {
    A11y,
    Autoplay,
    Navigation,
    Pagination,
    Scrollbar,
} from 'swiper';

SwiperCore.use([Navigation, Pagination, Scrollbar, A11y, Autoplay]);

@Component({
    selector: 'landing-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.scss'],
    encapsulation: ViewEncapsulation.None,
})
export class LandingHomeComponent {
    title = 'LEARNING';
    sub_title = 'MADE_SMART';
    siteIntro: string = '';
    siteIntro_bn: string = '';
    feedbacks: Feedback[] = [];
    partners: Partner[] = [];
    newsfeeds: Newsfeed[] = [];

    sliderItemsOrigin: any[] = [
        {
            img1: this.sanitize.bypassSecurityTrustResourceUrl(
                'assets/images/home/Slider1.webp'
            ),
            img2: this.sanitize.bypassSecurityTrustResourceUrl(
                'assets/images/home/home_mobile_view.webp'
            ),
        },
        {
            img1: this.sanitize.bypassSecurityTrustResourceUrl(
                'assets/images/home/Slider2.webp'
            ),
            img2: this.sanitize.bypassSecurityTrustResourceUrl(
                'assets/images/home/home_mobile_view.webp'
            ),
        },
    ];

    sliderItems = Array.from({ length: 4 }, () => [
        ...this.sliderItemsOrigin,
    ]).flat();

    constructor(
        private sanitize: DomSanitizer,
        private getDataApi: GetContentsApiService,
        private newsFeedApi: NewsfeedApiService
    ) {}

    ngOnInit() {
        this.getDataApi.getHomePageData().subscribe((res: any) => {
            if (res?.siteintro) {
                this.siteIntro = res?.siteintro[0]?.siteintro;
                this.siteIntro_bn = res?.siteintro[0]?.siteintro_bn;
                if (res.feedbacks) {
                    this.feedbacks = res.feedbacks;
                }
                if (res?.partners) {
                    this.partners = res?.partners;
                }
            }
        });

        this.newsFeedApi.getNewsFeedData().subscribe((res) => {
            if (res?.allnews) {
                this.newsfeeds = res?.allnews;
            }
        });
    }
}
