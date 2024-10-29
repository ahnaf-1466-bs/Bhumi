import { Component, Input, ViewChild } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ActivatedRoute, ParamMap } from '@angular/router';
import SwiperCore, { Virtual } from 'swiper';
import { SwiperComponent } from 'swiper/angular';
import { CourseDetailsService } from '../../course-details/course-details.service';
import { TranslateService } from '@ngx-translate/core';

SwiperCore.use([Virtual]);

@Component({
    selector: 'app-learn-from-slide',
    templateUrl: './learn-from-slide.component.html',
    styleUrls: ['./learn-from-slide.component.scss'],
})
export class LearnFromSlideComponent {
    @Input() names: string[];
    @Input() positions: string[];
    @Input() orgs: string[];

    @Input() image_urls: string[];
    @Input() descriptions: string[];

    @ViewChild('swiper', { static: false }) swiper?: SwiperComponent;
    slideNext() {
        this.swiper.swiperRef.slideNext(400);
    }
    slidePrev() {
        this.swiper.swiperRef.slidePrev(400);
    }

    active_id: number = 1;

    onClick(element_id: number) {
        this.active_id = element_id;
    }

    selectedPerson: any;

    selected(id: number) {
        this.selectedPerson = this.persons.find((person) => person.id === id);
    }

    persons: any[];

    id;

    ngOnInit() {
        this.route.paramMap.subscribe((params: ParamMap) => {
            this.id = +params.get('id');
        });

        let body = [
            {
                wsfunction:
                    'bs_webservicesuite_get_instructor_details_by_courseid',
            },
            { courseid: this.id },
        ];

        this._getCoursesService.getDetails(body).subscribe((response) => {
            this.persons = response.users;

            if(this.translateService.getDefaultLang()==='bn')
            {
                this.persons.forEach((p,index) => {
                    if(index>=0)
                    {
                        if(p.customfields?.length>5)
                        {
                            p.customfields[0]=p.customfields[4];
                            p.customfields[1]=p.customfields[5];
                        }
                    }
                });
            }
            
            this.selectedPerson = this.persons[1];
        });
    }

    constructor(
        private sanitize: DomSanitizer,
        private _getCoursesService: CourseDetailsService,
        private route: ActivatedRoute,
        private translateService:TranslateService
    ) {}

    @Input() mobile: boolean;
}
