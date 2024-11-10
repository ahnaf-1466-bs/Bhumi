import { Component, Input, ViewChild } from '@angular/core';
import { Instructor } from '../../models/instructor';
import SwiperCore, { Virtual } from 'swiper';
import { SwiperComponent } from 'swiper/angular';
import { ViewEncapsulation } from '@angular/core';
import { Founder } from '../../models/founder';

//SwiperCore.use([Virtual]);

import Swiper from 'swiper';


@Component({
  selector: 'app-founder',
  templateUrl: './founder.component.html',
  styleUrls: ['./founder.component.scss']
})
export class FounderComponent {
  @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
  @Input() founderList:Founder[];
  isBengali: boolean = false;
  slides: { founderList:Founder[] }[] = [];

  constructor() { }

 
  ngOnInit(): void {

      if (localStorage.getItem('lang') === 'bn') {
          this.isBengali = true;
      } else {
          this.isBengali = false;
      }
  
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
  }

  ngOnChanges(){
        for(let item of this.founderList){
            if(!item.foundername_bn){   
                item.foundername_bn = item.foundername;
            }
            if(!item.founderdeg_bn){   
                item.founderdeg_bn = item.founderdeg;
            }  
        }
  }

  
}