import { Component, ViewChild, ViewEncapsulation } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { DomSanitizer } from '@angular/platform-browser';
import SwiperCore, { Virtual } from 'swiper';
import { SwiperComponent } from 'swiper/angular';
import { BookASeatModal } from './book-a-seat-modal/book-a-seat-modal.component';
import { BookASeatService } from './services/book-a-seat.service';
import { ZendeskService } from './services/zendesk.service';

SwiperCore.use([Virtual]);
@Component({
    selector: 'app-experts-minds',
    templateUrl: './experts-minds.component.html',
    styleUrls: ['./experts-minds.component.scss'],
    encapsulation: ViewEncapsulation.None,
})
export class ExpertsMindsComponent {
    @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
    @ViewChild('swiper2', { static: false }) swiper2?: SwiperComponent;

    constructor(private sanitize: DomSanitizer,
        private dialog:MatDialog,
        private bookAseatAPI:BookASeatService,
        private zendeskAPI:ZendeskService
        ) {}

    sliderItemsOrigin: any[] = [
        {
            img: this.sanitize.bypassSecurityTrustResourceUrl(
                'assets/images/home/Experts-minds.webp'
            ),
        },
    ];

    onClickBookASeat():void
    {
        const dialogRef = this.dialog.open(BookASeatModal, {
            panelClass: 'custom-modalbox',
          });
      
          dialogRef.afterClosed().subscribe(result => {
            // send book a seat info to moodle backend
            this.bookAseatAPI.bookSeat(result)
                .subscribe((res)=>{
                    if (res.exception || res.errorcode) {
                        console.log("Error sending 'book a seat' info:",res)
                        return;
                    }
                    else if(res.status){
                        console.log("Success sending 'book a seat' info:",res)
                    }        
                })

            // create a ticket in zendesk (auto send a confirmation mail to user)
            this.zendeskAPI.createTicket(result)
                .subscribe((res)=>{
                    console.log("successfuly created a ticket in zendesk: ",res);
                })
          });
    }        
    

    sliderItems = Array.from({ length: 4 }, () => [
        ...this.sliderItemsOrigin,
    ]).flat();

    slideNextExperts() {
        this.swiper?.swiperRef.slideNext(100);
    }
    slideNextExpertsPhone() {
        this.swiper2?.swiperRef.slideNext(100);
    }
    slidePrevExperts() {
        this.swiper?.swiperRef.slidePrev(100);
    }
    slidePrevExpertsPhone() {
        this.swiper2?.swiperRef.slidePrev(100);
    }
}
