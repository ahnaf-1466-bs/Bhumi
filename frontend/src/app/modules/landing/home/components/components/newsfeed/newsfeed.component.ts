import {
    Component,
    Input,
    OnInit,
    ViewChild,
    ViewEncapsulation,
} from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Router } from '@angular/router';
import { SwiperComponent } from 'swiper/angular/swiper-angular';
import { Newsfeed } from '../../../models/newsfeed';
import { ShareModalComponent } from './share-modal/share-modal.component';

@Component({
    selector: 'app-newsfeed',
    templateUrl: './newsfeed.component.html',
    styleUrls: ['./newsfeed.component.scss'],
    encapsulation: ViewEncapsulation.None
})
export class NewsfeedComponent implements OnInit {
    @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
    @ViewChild('swiper2', { static: false }) swiper2?: SwiperComponent;

    @Input() newsFeeds: Newsfeed[];

    constructor(private _router: Router, private dialog2: MatDialog) {}

    isBengali: boolean = false;

    ngOnInit() {
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }
    }

    slideNextExperts() {
        this.swiper.swiperRef.slideNext(100);
    }
    slideNextExpertsPhone() {
        this.swiper2.swiperRef.slideNext(100);
    }
    slidePrevExperts() {
        this.swiper.swiperRef.slidePrev(100);
    }
    slidePrevExpertsPhone() {
        this.swiper2.swiperRef.slidePrev(100);
    }

    displayNewsDetails(id) {
        this._router.navigate(['newsfeed', id]);
    }

    openShareModal(newsID) {
        const dialogRef = this.dialog2.open(ShareModalComponent, {
            data: { id: newsID },
        });
        dialogRef.afterClosed().subscribe((result) => {
            // console.log(`Dialog result: ${result}`);
        });
    }
}
