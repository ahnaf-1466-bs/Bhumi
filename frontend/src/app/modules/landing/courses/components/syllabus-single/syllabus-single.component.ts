import { Component, Input } from '@angular/core';

@Component({
    selector: 'app-syllabus-single',
    templateUrl: './syllabus-single.component.html',
    styleUrls: ['./syllabus-single.component.scss'],
})
export class SyllabusSingleComponent {
    @Input() syllabus_title: string;
    @Input() syllabus_items: string;

    @Input() mobile: boolean;
}
