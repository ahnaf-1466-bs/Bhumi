import { Component, Input } from '@angular/core';
import SwiperCore, { Virtual } from 'swiper';

SwiperCore.use([Virtual]);
@Component({
    selector: 'app-program-slide',
    templateUrl: './program-slide.component.html',
    styleUrls: ['./program-slide.component.scss'],
})
export class ProgramSlideComponent {
    @Input() fee: string;
    @Input() mobile: boolean;
    @Input() enrolled: boolean;
    @Input() program_pdf: any;
    @Input() header_month: string;
    @Input() program_dates: any[];
    @Input() program_year: string;
    @Input() course_length: string;
    @Input() result_from_parent: number;
    @Input() application_deadline: string;
    @Input() program_details:any;
    @Input() programname:any;
}
