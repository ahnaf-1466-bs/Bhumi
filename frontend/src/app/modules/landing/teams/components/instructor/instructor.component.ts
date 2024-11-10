import { Component, Input, ViewChild } from '@angular/core';
import { Instructor } from '../../models/instructor';
import { SwiperComponent } from 'swiper/angular';
import { Router } from '@angular/router';
import Swiper from 'swiper';


@Component({
  selector: 'app-instructor',
  templateUrl: './instructor.component.html',
  styleUrls: ['./instructor.component.scss'],
})


export class InstructorComponent {
  @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
  @Input() instructorList:Instructor[];
  isBengali: boolean = false;

 slides: { instructorList:Instructor[] }[] = [];

 constructor(private _router:Router) { }

 ngOnInit(): void {
      if (localStorage.getItem('lang') === 'bn') {
          this.isBengali = true;
      } else {
          this.isBengali = false;
      }
 }


 ngAfterViewInit(): void {
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
         spaceBetween: 10,
       },
       925: {
         slidesPerView: 2,
         spaceBetween: 10,
       },
       1024: {
         slidesPerView: 3,
         spaceBetween: 10,
       },
     },
   });
 }

 goToCourses(){
    this._router.navigate(['courses']);
 }

 ngOnChanges(){
  
  for(let item of this.instructorList){
      if(!item.instructorname_bn){   
          item.instructorname_bn = item.instructorname;
      }
      if(!item.instructordeg_bn){   
          item.instructordeg_bn = item.instructordeg;
      }  
  }
}


 


 
}
