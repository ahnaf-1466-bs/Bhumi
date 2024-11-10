import { Component, Input, ViewChild } from '@angular/core';
import { Course } from 'app/modules/landing/dashboard/models/course';
import { SwiperComponent } from 'swiper/angular';
import Swiper from 'swiper';
import { Router } from '@angular/router';
import { UpcomingCourseService } from 'app/modules/landing/dashboard/services/upcoming-course.service';

@Component({
  selector: 'app-wishlisted-course',
  templateUrl: './wishlisted-course.component.html',
  styleUrls: ['./wishlisted-course.component.scss']
})
export class WishlistedCourseComponent {

      @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
      @Input() favCourses:Course[] = [];
      slides: { upcomingCourseList:Course[] }[] = [];
     

  
      constructor(
        private upcomingCourseApi: UpcomingCourseService,
          private _router: Router
      ) {}

      ngOnInit() {}

      ngAfterViewInit(){
          const swiperContainer = document.querySelector('.swiper-container-wishlisted');
          if (swiperContainer) {
          new Swiper('.swiper-container-wishlisted', {
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

      displayCourseDetails(id) {
        this._router.navigate(['course', id]);
      }


      unfavourite(courseID){
            this.upcomingCourseApi.setFavourite(courseID, 0).subscribe( (res:any)=>{
                  if( !res.exception ){
                       
                        const index = this.favCourses.findIndex((value) => value.id == courseID );
                        this.favCourses.splice(index, 1);

                  }
            })
      }


}
